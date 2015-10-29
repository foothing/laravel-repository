<?php

class EloquentCriteriaTest extends PHPUnit_Framework_TestCase {

	function testFilter() {
		$criteria = new \Foothing\Repository\Eloquent\EloquentCriteria();
		$criteria->filter('name', 'Homer');
		$criteria->filter('lastName', 'Simpson');
		$this->assertEquals(2, count($criteria->filters));
		$this->assertEquals('name', $criteria->filters[0]->field);
		$this->assertEquals('Homer', $criteria->filters[0]->value);
		$this->assertEquals('=', $criteria->filters[0]->operator);
		$this->assertEquals('lastName', $criteria->filters[1]->field);
		$this->assertEquals('Simpson', $criteria->filters[1]->value);
		$this->assertEquals('=', $criteria->filters[1]->operator);

		$criteria->resetFilters()->filter('foo', 'bar', '<');
		$this->assertEquals('<', $criteria->filters[0]->operator);
		$criteria->resetFilters()->filter('foo', 'bar', '<=');
		$this->assertEquals('<=', $criteria->filters[0]->operator);
		$criteria->resetFilters()->filter('foo', 'bar', '>');
		$this->assertEquals('>', $criteria->filters[0]->operator);
		$criteria->resetFilters()->filter('foo', 'bar', '>=');
		$this->assertEquals('>=', $criteria->filters[0]->operator);
		$criteria->resetFilters()->filter('foo', 'bar', '!=');
		$this->assertEquals('!=', $criteria->filters[0]->operator);

		$this->setExpectedException('Exception');
		$criteria->resetFilters()->filter('foo', 'bar', 'invalid');
	}

	function testOrder() {
		$criteria = new \Foothing\Repository\Eloquent\EloquentCriteria();
		$criteria->order('foo')->sort('asc');
		$this->assertEquals('foo', $criteria->orderBy);
		$this->assertEquals('asc', $criteria->sortDirection);

		$criteria->order('foo')->sort('desc');
		$this->assertEquals('foo', $criteria->orderBy);
		$this->assertEquals('desc', $criteria->sortDirection);

		$criteria->order('foo')->sort('xyz');
		$this->assertEquals('foo', $criteria->orderBy);
		$this->assertEquals('asc', $criteria->sortDirection);
	}

	function testMake() {
		\Mockery::mock('alias:Illuminate\Support\Facades\Log')->shouldReceive('debug');
		$qparams = [
			'filter' => (object)[
				'fields' => [
					(object)['name' => 'email', 'value' => 'test', 'operator' => '<='],
					(object)['name' => 'country', 'value' => 'Italy', 'operator' => 'like']
				]
			],
			'sort' => (object)[
				'field' => 'foo',
				'direction' => 'asc'
			]
		];
		$qparams = json_encode( (object)$qparams );
		$query = \Foothing\Request\Laravel\RemoteQuery::spawn($qparams);
		$criteria = (new \Foothing\Repository\Eloquent\EloquentCriteria())->make($query);
		$this->assertEquals(2, count($criteria->filters));
		$this->assertEquals('email', $criteria->filters[0]->field);
		$this->assertEquals('test', $criteria->filters[0]->value);
		$this->assertEquals('<=', $criteria->filters[0]->operator);
		$this->assertEquals('foo', $criteria->orderBy);
		$this->assertEquals('asc', $criteria->sortDirection);
	}

}