<?php namespace Foothing\Repository\Tests\Repository\Operators;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\Operators\DefaultOperator;

class DefaultOperatorTest extends \PHPUnit_Framework_TestCase {

    public function testApply_with_simple_field() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('where')
            ->with('foo', '=', 'bar')
            ->getMock();

        $filter = new CriteriaFilter('foo', 'bar', '=');
        (new DefaultOperator($filter))->apply($query);
    }

    public function testApply_with_nested_field() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('whereHas')
            ->getMock();

        $filter = new CriteriaFilter('foo.bar', 'baz', '=');
        (new DefaultOperator($filter))->apply($query);
    }

    public function tearDown() {
        \Mockery::close();
    }
}
