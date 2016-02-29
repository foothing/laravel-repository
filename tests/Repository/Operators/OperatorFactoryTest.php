<?php namespace Foothing\Repository\Tests\Repository\Operators;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\Operators\DefaultOperator;
use Foothing\Repository\Operators\InOperator;
use Foothing\Repository\Operators\OperatorFactory;

class OperatorFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testValidate() {
        $this->assertTrue(OperatorFactory::validate('='));
        $this->assertTrue(OperatorFactory::validate('like'));
        $this->assertTrue(OperatorFactory::validate('<'));
        $this->assertTrue(OperatorFactory::validate('<='));
        $this->assertTrue(OperatorFactory::validate('>'));
        $this->assertTrue(OperatorFactory::validate('>='));
        $this->assertTrue(OperatorFactory::validate('!='));
        $this->assertTrue(OperatorFactory::validate('in'));
        $this->assertFalse(OperatorFactory::validate(' '));
        $this->assertFalse(OperatorFactory::validate(null));
    }

    public function testMake() {
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '=')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', 'like')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '<')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '<=')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '>')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '>=')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', '!=')) instanceof DefaultOperator);
        $this->assertTrue(OperatorFactory::make(new CriteriaFilter('foo', 'bar', 'in')) instanceof InOperator);
    }
}
