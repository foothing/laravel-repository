<?php namespace Foothing\Repository\Tests\Repository\Operators;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\Operators\InOperator;

class InOperatorTest extends \PHPUnit_Framework_TestCase {

    public function testApplyWithComma() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('whereIn')
            ->with('foo', ['bar', 'baz'])
            ->getMock();

        $filter = new CriteriaFilter('foo', 'bar,baz', 'in');
        (new InOperator($filter))->apply($query);
    }

    public function testApplyWithArray() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('whereIn')
            ->with('foo', ['bar', 'baz'])
            ->getMock();

        $filter = new CriteriaFilter('foo', ['bar', 'baz'], 'in');
        (new InOperator($filter))->apply($query);
    }

    public function testApplyWithSingleArg() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('whereIn')
            ->with('foo', ['bar'])
            ->getMock();

        $filter = new CriteriaFilter('foo', 'bar', 'in');
        (new InOperator($filter))->apply($query);

        $filter = new CriteriaFilter('foo', ['bar'], 'in');
        (new InOperator($filter))->apply($query);
    }

    public function tearDown() {
        \Mockery::close();
    }
}
