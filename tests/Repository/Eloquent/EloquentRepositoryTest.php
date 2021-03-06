<?php namespace Foothing\Repository\Tests\Repository\Eloquent;

use Foothing\Repository\Tests\BaseTestCase;
use Foothing\Repository\Tests\Fixtures\Person;
use Foothing\Repository\Tests\Fixtures\Role;

class EloquentRepositoryTest extends BaseTestCase {
    /**
     * @var Repository
     */
    protected $repository;

    public function setUp() {
        parent::setUp();
        $this->repository = new \Foothing\Repository\Eloquent\EloquentRepository(new Person());
    }

    public function testFind() {
        $person = $this->repository->find(0);
        $this->assertNull($person);

        $person = $this->repository->find(null);
        $this->assertNull($person);

        $person = $this->repository->find(1);
        $this->assertNotNull($person);
        $this->assertEquals('Homer', $person->name);
    }

    public function testFindUsingScope() {
        $homer = $this->repository->scope('male')->find(1);
        $this->assertEquals("Homer", $homer->name);

        $marge = $this->repository->scope('male')->find(2);
        $this->assertNull($marge);
    }

    public function testFindWith() {
        $person = $this->repository->with([])->find(1);
        $this->assertNotNull($person);
        $this->assertEquals('Homer', $person->name);

        $person = $this->repository->with(['roles'])->find(1);
        // Encode and decode to prevent lazy loading.
        $person = json_decode(json_encode($person));
        $this->assertNotNull($person);
        $this->assertEquals('Homer', $person->name);
        $this->assertNotNull($person->roles);
        $this->assertEquals('Father', $person->roles[0]->name);

        $person = $this->repository->with(['roles', 'children'])->find(1);
        // Encode and decode to prevent lazy loading.
        $person = json_decode(json_encode($person));
        $this->assertNotNull($person);
        $this->assertEquals('Homer', $person->name);
        $this->assertNotNull($person->roles);
        $this->assertEquals('Father', $person->roles[0]->name);
        $this->assertNotNull($person->children);
        $this->assertEquals('Bart', $person->children[0]->name);
        $this->assertEquals('Lisa', $person->children[1]->name);
        $this->assertEquals('Maggie', $person->children[2]->name);
    }

    public function testAll() {
        $people = $this->repository->all();
        $this->assertEquals(3, count($people));
        $this->assertEquals('Homer', $people[0]->name);
        $this->assertEquals('Marge', $people[1]->name);
        $this->assertEquals('Apu', $people[2]->name);
    }

    public function testAllUsingScope() {
        $malePeople = $this->repository->scope('male')->all();
        $this->assertEquals(2, $malePeople->count());
        $this->assertEquals("Homer", $malePeople[0]->name);
        $this->assertEquals("Apu", $malePeople[1]->name);
    }

    public function testAllUsingGlobaleScope() {
        $repository = new \PersonRepository(new Person());
        $malePeople = $repository->all();
        $this->assertEquals(1, $malePeople->count());
        $this->assertEquals("Homer", $malePeople[0]->name);
    }

    public function testAllWith() {
        $people = $this->repository->with(['roles', 'children'])->all();
        $this->assertEquals(3, count($people));
        $this->assertEquals('Homer', $people[0]->name);
        $this->assertEquals('Marge', $people[1]->name);
        $this->assertEquals('Apu', $people[2]->name);

        // Encode and decode to prevent lazy loading.
        $person = json_decode(json_encode($people[0]));
        $this->assertNotNull($person->roles);
        $this->assertEquals('Father', $person->roles[0]->name);
        $this->assertNotNull($person->children);
        $this->assertEquals('Bart', $person->children[0]->name);
        $this->assertEquals('Lisa', $person->children[1]->name);
        $this->assertEquals('Maggie', $person->children[2]->name);
    }

    public function testAllWithCriteria() {
        $people = $this->repository->filter('name', 'Homer')->all();
        $this->assertEquals(1, count($people));

        $people = $this->repository->filter('id', 1, '>')->all();
        $this->assertEquals(2, count($people));

        $people = $this->repository->filter('id', 1, '>=')->all();
        $this->assertEquals(3, count($people));

        $people = $this->repository->filter('id', 1, '!=')->all();
        $this->assertEquals(2, count($people));

        $people = $this->repository->filter('name', 'Homer,Marge', 'in')->all();
        $this->assertEquals(2, count($people));

        $people = $this->repository->filter('name', ['Homer', 'Marge'], 'in')->all();
        $this->assertEquals(2, count($people));

        $people = $this->repository->order('name')->all();
        $this->assertEquals('Apu', $people[0]->name);
        $this->assertEquals('Homer', $people[1]->name);
        $this->assertEquals('Marge', $people[2]->name);

        $people = $this->repository->order('name')->sort('desc')->all();
        $this->assertEquals('Apu', $people[2]->name);
    }

    public function testPaginate() {
        $people = $this->repository->paginate();
        $this->assertEquals(3, $people->total());
        $people = $people->items();
        $this->assertEquals(3, count($people));
        $this->assertEquals('Homer', $people[0]->name);
        $this->assertEquals('Marge', $people[1]->name);
        $this->assertEquals('Apu', $people[2]->name);
    }

    public function testPaginateUsingScope() {
        $malePeople = $this->repository->scope('male')->paginate();
        $this->assertEquals(2, $malePeople->total());
        $malePeople = $malePeople->items();
        $this->assertEquals("Homer", $malePeople[0]->name);
        $this->assertEquals("Apu", $malePeople[1]->name);
    }

    public function testPaginateUsingGlobaleScope() {
        $repository = new \PersonRepository(new Person());
        $funnyPeople = $repository->paginate();
        $this->assertEquals(1, $funnyPeople->total());
        $funnyPeople = $funnyPeople->items();
        $this->assertEquals("Homer", $funnyPeople[0]->name);
    }

    public function testPaginateWith() {
        $people = $this->repository->with(['roles', 'children'])->paginate();
        $this->assertEquals(3, $people->total());
        $people = $people->items();
        $person = json_decode(json_encode($people[0]));
        $this->assertNotNull($person->roles);
        $this->assertEquals('Father', $person->roles[0]->name);
        $this->assertNotNull($person->children);
        $this->assertEquals('Bart', $person->children[0]->name);
        $this->assertEquals('Lisa', $person->children[1]->name);
        $this->assertEquals('Maggie', $person->children[2]->name);
    }

    public function testPaginateWithCriteria() {
        $people = $this->repository->filter('name', 'Homer')->paginate();
        $this->assertEquals(1, $people->total());

        $people = $this->repository->filter('id', 1, '>')->paginate();
        $this->assertEquals(2, $people->total());

        $people = $this->repository->filter('id', 1, '>=')->paginate();
        $this->assertEquals(3, $people->total());

        $people = $this->repository->filter('id', 1, '!=')->paginate();
        $this->assertEquals(2, $people->total());

        $people = $this->repository->filter('name', 'Homer,Marge', 'in')->paginate();
        $this->assertEquals(2, $people->total());

        $people = $this->repository->filter('name', ['Homer', 'Marge'], 'in')->paginate();
        $this->assertEquals(2, $people->total());

        $people = $this->repository->order('name')->paginate();
        $people = $people->items();
        $this->assertEquals('Apu', $people[0]->name);
        $this->assertEquals('Homer', $people[1]->name);
        $this->assertEquals('Marge', $people[2]->name);

        $people = $this->repository->order('name')->sort('desc')->paginate();
        $people = $people->items();
        $this->assertEquals('Apu', $people[2]->name);
    }

    public function testCount() {
        $this->assertEquals(3, $this->repository->count());
    }

    public function testCountWithFilters() {
        $this->assertEquals(2, $this->repository->filter('id', 1, '>')->count());
    }

    public function testCountWithScope() {
        $this->assertEquals(2, $this->repository->scope('male')->count());
    }

    public function testCountWithGlobalScope() {
        $repository = new \PersonRepository(new Person());
        $funnyPeople = $repository->count();
        $this->assertEquals(1, $funnyPeople);
    }

    public function testCreate() {
        $person = new Person();
        $person = $this->repository->create($person);
        $this->assertNotNull($person->id);
    }

    public function testCreateWith() {
        $person = new Person();
        $person = $this->repository->with(['roles'])->create($person);
        $person = json_decode(json_encode($person));
        $this->assertTrue(property_exists($person, 'roles'));
        $this->assertEmpty($person->roles);
    }

    public function testUpdate() {
        $person = new Person();
        $this->setExpectedException('Exception');
        $this->repository->update($person);

        $person = $this->repository->create($person);
        $person->name = 'Foo';
        $person = $this->repository->update($person);
        $this->assertEquals('Foo', $person->name);
    }

    public function testUpdateWith() {
        $person = new Person();
        $person = $this->repository->create($person);
        $person->name = 'Foo';
        $person = $this->repository->with(['roles'])->update($person);
        $person = json_decode(json_encode($person));
        $this->assertEquals('Foo', $person->name);
        $this->assertEmpty($person->roles);
    }

    public function testAutoEagerLoading() {
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
        $simpsons = json_decode(json_encode($simpsons));
        $this->assertEquals(1, count($simpsons[0]->roles));
        $this->assertFalse(property_exists($simpsons[0], 'children'));

        // Check that repository with() method override default settings.
        $simpsons = $this->repository->with(['roles', 'children'])->all();
        $simpsons = json_decode(json_encode($simpsons));
        $this->assertEquals(1, count($simpsons[0]->roles));
        $this->assertEquals(3, count($simpsons[0]->children));
    }

    public function testFindOneBy() {
        $homer = $this->repository->findOneBy('name', 'Homer');
        $this->assertEquals('Homer', $homer->name);
    }

    public function testFindOneByUsingScope() {
        $homer = $this->repository->scope('male')->findOneBy('name', 'Homer');
        $this->assertEquals("Homer", $homer->name);

        $marge = $this->repository->scope('male')->findOneBy('name', 'Marge');
        $this->assertNull($marge);
    }

    public function testFindOneByUsingGlobalScope() {
        $repository = new \PersonRepository(new Person());
        $homer = $repository->findOneBy('name', 'Homer');
        $this->assertEquals("Homer", $homer->name);

        $marge = $repository->findOneBy('name', 'Marge');
        $this->assertNull($marge);
    }

    public function testFindAllBy() {
        $people = $this->repository->findAllBy('id', 1, '>');
        $this->assertEquals(2, $people->count());
        $this->assertEquals('Marge', $people[0]->name);
        $this->assertEquals('Apu', $people[1]->name);
    }

    public function testFindAllByUsingScope() {
        $malePeople = $this->repository->scope('male')->findAllBy('id', 1, '>');
        $this->assertEquals(1, $malePeople->count());
        $this->assertEquals("Apu", $malePeople[0]->name);
    }

    public function testFindAllByUsingGlobaleScope() {
        $repository = new \PersonRepository(new Person());
        $funnyPeople = $repository->findAllBy('id', 4, '<');
        $this->assertEquals(1, $funnyPeople->count());
        $this->assertEquals("Homer", $funnyPeople[0]->name);
    }

    public function testUpdateSkipsRelations() {
        $homer = $this->repository->findOneBy('name', 'Homer');
        $homer->children = (object)['pid' => 1, 'name' => 'Bart'];
        $homer->name = 'Foo';
        // Expect no exceptions.
        $this->repository->update($homer);
    }

    public function testCriteriaShortcutOnAll() {
        $simpsons = $this->repository->filter('id', 1, '>')->order('name')->all();
        $this->assertEquals(2, $simpsons->count());
        $this->assertEquals('Apu', $simpsons[0]->name);
        $this->assertEquals('Marge', $simpsons[1]->name);
        $simpsons = $this->repository->filter('id', 1, '>')->order('name', 'desc')->all();
        $this->assertEquals(2, $simpsons->count());
        $this->assertEquals('Apu', $simpsons[1]->name);
        $this->assertEquals('Marge', $simpsons[0]->name);
    }

    public function testCriteriaIsOverriden() {
        $this->repository = $this->repository->filter('id', 1, '>')->order('name');
        $criteria = new \Foothing\Repository\Eloquent\EloquentCriteria();
        $criteria->filter('id', 1);
        $homer = $this->repository->criteria($criteria)->all();
        $this->assertEquals(1, $homer->count());
        $this->assertEquals('Homer', $homer[0]->name);
    }

    public function testAllWithNestedFilter() {
        $people = $this->repository->filter('children.name', 'Lisa')->all();
        $this->assertEquals(2, count($people));
        $this->assertEquals("Homer", $people[0]->name);
        $this->assertEquals("Marge", $people[1]->name);
    }

    public function testAttach() {
        $foodCritic = Role::find(7);
        $homer = $this->repository->findOneBy('name', 'Homer');

        // Check preconditions.
        $this->assertEquals(1, $homer->roles->count());

        $homer = $this->repository->attach($homer, 'roles', $foodCritic);

        // Refresh.
        $homer->load('roles');

        // Homer is now a father and a food critic.
        $this->assertEquals(2, $homer->roles->count());
        $this->assertEquals($foodCritic->id, $homer->roles[1]->id);
    }

    public function testAttach_fails_if_relation_is_not_many_to_many() {
        $this->setExpectedException('Exception');
        $this->repository->attach(Person::find(1), 'children', new \Son());
    }

    public function testDetach() {
        $homer = $this->repository->findOneBy('name', 'Homer');
        $father = Role::find(1);

        $this->assertEquals(1, $homer->roles->count());

        $homer = $this->repository->detach($homer, 'roles', $father);

        // Refresh.
        $homer->load('roles');

        $this->assertEquals(0, $homer->roles->count());
    }

    public function testDetach_fails_if_relation_is_not_many_to_many() {
        $this->setExpectedException('Exception');
        $this->repository->detach(Person::find(1), 'children', new \Son());
    }

    public function test_scope_overwrite_global_scope() {
        $repository = new \PersonRepository(new Person());
        $malePeople = $repository->scope('male')->all();
        $this->assertEquals(2, $malePeople->count());
        $this->assertEquals("Homer", $malePeople[0]->name);
        $this->assertEquals("Apu", $malePeople[1]->name);
    }
}
