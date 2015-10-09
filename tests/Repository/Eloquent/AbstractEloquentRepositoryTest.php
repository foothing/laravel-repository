<?php

class AbstractEloquentRepositoryTest extends BaseTestCase {
	protected $repository;

	function setUp() {
		parent::setUp();
		$this->repository = new Repository( new Person() );
	}

	function testFind() {
		$person = $this->repository->find(0);
		$this->assertNull($person);

		$person = $this->repository->find(null);
		$this->assertNull($person);

		$person = $this->repository->find(1);
		$this->assertNotNull($person);
		$this->assertEquals('Homer', $person->name);
	}

	function testFindWith() {
		$person = $this->repository->with([])->find(1);
		$this->assertNotNull($person);
		$this->assertEquals('Homer', $person->name);

		$person = $this->repository->with(['roles'])->find(1);
		// Encode and decode to prevent lazy loading.
		$person = json_decode( json_encode($person) );
		$this->assertNotNull($person);
		$this->assertEquals('Homer', $person->name);
		$this->assertNotNull($person->roles);
		$this->assertEquals('Father', $person->roles[0]->name);

		$person = $this->repository->with(['roles', 'children'])->find(1);
		// Encode and decode to prevent lazy loading.
		$person = json_decode( json_encode($person) );
		$this->assertNotNull($person);
		$this->assertEquals('Homer', $person->name);
		$this->assertNotNull($person->roles);
		$this->assertEquals('Father', $person->roles[0]->name);
		$this->assertNotNull($person->children);
		$this->assertEquals('Bart', $person->children[0]->name);
		$this->assertEquals('Lisa', $person->children[1]->name);
		$this->assertEquals('Maggie', $person->children[2]->name);
	}

	function testAll() {
		$people = $this->repository->all();
		$this->assertEquals(3, count($people));
		$this->assertEquals('Homer', $people[0]->name);
		$this->assertEquals('Marge', $people[1]->name);
		$this->assertEquals('Apu', $people[2]->name);
	}

	function testAllWith() {
		$people = $this->repository->with(['roles', 'children'])->all();
		$this->assertEquals(3, count($people));
		$this->assertEquals('Homer', $people[0]->name);
		$this->assertEquals('Marge', $people[1]->name);
		$this->assertEquals('Apu', $people[2]->name);

		// Encode and decode to prevent lazy loading.
		$person = json_decode( json_encode($people[0]) );
		$this->assertNotNull($person->roles);
		$this->assertEquals('Father', $person->roles[0]->name);
		$this->assertNotNull($person->children);
		$this->assertEquals('Bart', $person->children[0]->name);
		$this->assertEquals('Lisa', $person->children[1]->name);
		$this->assertEquals('Maggie', $person->children[2]->name);
	}

	function testPaginate() {
		$people = $this->repository->paginate();
		$this->assertEquals(3, $people->total());
		$people = $people->items();
		$this->assertEquals(3, count($people));
		$this->assertEquals('Homer', $people[0]->name);
		$this->assertEquals('Marge', $people[1]->name);
		$this->assertEquals('Apu', $people[2]->name);
	}

	function testPaginateWith() {
		$people = $this->repository->with(['roles', 'children'])->paginate();
		$this->assertEquals(3, $people->total());
		$people = $people->items();
		$person = json_decode( json_encode($people[0]) );
		$this->assertNotNull($person->roles);
		$this->assertEquals('Father', $person->roles[0]->name);
		$this->assertNotNull($person->children);
		$this->assertEquals('Bart', $person->children[0]->name);
		$this->assertEquals('Lisa', $person->children[1]->name);
		$this->assertEquals('Maggie', $person->children[2]->name);
	}

	function testPaginateCriteria() {
		$criteria = new \Foothing\Common\Repository\Eloquent\EloquentCriteria();
		$criteria->filter('name', 'Homer');
		$people = $this->repository->paginate($criteria);
		$this->assertEquals(1, $people->total());

		$criteria->resetFilters()->filter('id', 1, '>');
		$people = $this->repository->paginate($criteria);
		$this->assertEquals(2, $people->total());

		$criteria->resetFilters()->filter('id', 1, '>=');
		$people = $this->repository->paginate($criteria);
		$this->assertEquals(3, $people->total());

		$criteria->resetFilters()->filter('id', 1, '!=');
		$people = $this->repository->paginate($criteria);
		$this->assertEquals(2, $people->total());

		$criteria->resetFilters()->order('name');
		$people = $this->repository->paginate($criteria);
		$people = $people->items();
		$this->assertEquals('Apu', $people[0]->name);
		$this->assertEquals('Homer', $people[1]->name);
		$this->assertEquals('Marge', $people[2]->name);

		$criteria->resetFilters()->order('name', 'desc');
		$people = $this->repository->paginate($criteria);
		$people = $people->items();
		$this->assertEquals('Apu', $people[2]->name);
	}

	function testCreate() {
		$person = new Person();
		$person = $this->repository->create($person);
		$this->assertNotNull($person->id);
	}

	function testCreateWith() {
		$person = new Person();
		$person = $this->repository->with(['roles'])->create($person);
		$person = json_decode( json_encode($person) );
		$this->assertTrue(property_exists($person, 'roles'));
		$this->assertEmpty($person->roles);
	}

	function testUpdate() {
		$person = new Person();
		$this->setExpectedException('Exception');
		$this->repository->update($person);

		$person = $this->repository->create($person);
		$person->name = 'Foo';
		$person = $this->repository->update($person);
		$this->assertEquals('Foo', $person->name);
	}

	function testUpdateWith() {
		$person = new Person();
		$person = $this->repository->create($person);
		$person->name = 'Foo';
		$person = $this->repository->with(['roles'])->update($person);
		$person = json_decode( json_encode($person) );
		$this->assertEquals('Foo', $person->name);
		$this->assertEmpty($person->roles);
	}

	function testAutoEagerLoading() {
		$person = new Person();

		// We expect a new person to contain the
		// relations defined with the ResourceInterface 'unit'.
		$person = $this->repository->create($person);
		$this->assertEquals(0, $person->roles->count());
		$this->assertEquals(0, $person->children->count());

		// Check auto-eager-loading.
		$homer = $this->repository->find(1);
		$this->assertEquals(1, $homer->roles->count());
		$this->assertEquals(3, $homer->children->count());

		$simpsons = $this->repository->all();
		$simpsons = json_decode( json_encode($simpsons) );
		$this->assertEquals(1, count($simpsons[0]->roles));
		$this->assertFalse(property_exists($simpsons[0], 'children'));

		// Check that repository with() method override default settings.
		$simpsons = $this->repository->with(['roles', 'children'])->all();
		$simpsons = json_decode( json_encode($simpsons) );
		$this->assertEquals(1, count($simpsons[0]->roles));
		$this->assertEquals(3, count($simpsons[0]->children));
	}

	function testFindOneBy() {
		$homer = $this->repository->findOneBy('name', 'Homer');
		$this->assertEquals('Homer', $homer->name);
	}

	function testFindAllBy() {
		$people = $this->repository->findAllBy('id', 1, '>');
		$this->assertEquals(2, $people->count());
		$this->assertEquals('Marge', $people[0]->name);
		$this->assertEquals('Apu', $people[1]->name);
	}

	function testUpdateSkipsRelations() {
		$homer = $this->repository->findOneBy('name', 'Homer');
		$homer->children = (object)['pid' => 1, 'name' => 'Bart'];
		$homer->name = 'Foo';
		// Expect no exceptions.
		$this->repository->update($homer);
	}
}

class Repository extends \Foothing\Common\Repository\Eloquent\AbstractEloquentRepository {

}