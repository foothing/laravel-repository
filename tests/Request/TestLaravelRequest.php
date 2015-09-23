<?php

use Foothing\Common\Request\Laravel\RemoteQuery;

class TestLaravelRequest extends PHPUnit_Framework_TestCase {

	public function testQuerySpawnsProperly() {
		\Mockery::mock('alias:Illuminate\Support\Facades\Log')->shouldReceive('debug');

		$qparams = [];

		// Check empty query.
		$query = $this->spawn($qparams);
		$this->assertFalse($query->sortEnabled);
		$this->assertNull($query->sortField);
		$this->assertEquals('asc', $query->sortDirection);
		$this->assertFalse($query->filterEnabled);
		$this->assertNull($query->filters);

		// No exceptions for sort command without sort field.
		$qparams = ['sort'];
		$query = $this->spawn($qparams);

		$qparams = [ 'sort' => null ];
		$query = $this->spawn($qparams);

		// Check sort query (default asc).
		$qparams = [ 'sort' => (object)['field' => 'name'] ];
		$query = $this->spawn($qparams);
		$this->assertTrue($query->sortEnabled);
		$this->assertNotNull($query->sortField);
		$this->assertEquals('asc', $query->sortDirection);
		$this->assertFalse($query->filterEnabled);
		$this->assertNull($query->filters);

		// Check sort direction.
		$qparams = [ 'sort' => (object)['field' => 'name', 'direction' => 'desc'] ];
		$query = $this->spawn($qparams);
		$this->assertTrue($query->sortEnabled);
		$this->assertNotNull($query->sortField);
		$this->assertEquals('desc', $query->sortDirection);
		$this->assertFalse($query->filterEnabled);
		$this->assertNull($query->filters);

		// Check filters.
		$qparams = [ 'filter' => null ];
		$query = $this->spawn($qparams);

		$qparams = [ 'filter' => (object)[
			'fields' => [
				(object)['name' => 'email', 'value' => 'test', 'operator' => '<='],
				(object)['name' => 'country', 'value' => 'Italy', 'operator' => 'like']
			]
		]];
		$query = $this->spawn($qparams);
		$this->assertFalse($query->sortEnabled);
		$this->assertNull($query->sortField);
		$this->assertEquals('asc', $query->sortDirection);
		$this->assertTrue($query->filterEnabled);
		$this->assertNotNull($query->filters);
		$this->assertEquals('email', $query->filters[0]->name);
		$this->assertEquals('test', $query->filters[0]->value);
		$this->assertEquals('<=', $query->filters[0]->operator);
		$this->assertEquals('country', $query->filters[1]->name);
		$this->assertEquals('Italy', $query->filters[1]->value);
		$this->assertEquals('like', $query->filters[1]->operator);

		//var_dump($query);
	}

	protected function spawn($qparams) {
		$qparams = json_encode( (object)$qparams );
		return RemoteQuery::spawn($qparams);
	}

}