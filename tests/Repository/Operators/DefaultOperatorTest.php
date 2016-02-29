<?php namespace Foothing\Repository\Tests\Repository\Operators;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\Operators\DefaultOperator;

class DefaultOperatorTest extends \PHPUnit_Framework_TestCase {

    public function testApply() {
        $query = \Mockery::mock('Illuminate\Database\Query\Builder')
            ->shouldReceive('where')
            ->with('foo', '=', 'bar')
            ->getMock();

        $filter = new CriteriaFilter('foo', 'bar', '=');
        (new DefaultOperator($filter))->apply($query);
    }

    public function tearDown() {
        \Mockery::close();
    }
}
