<?php namespace Foothing\Repository\Tests\Repository\Operators;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\Operators\AbstractOperator;

class AbstractOperatorTest extends \PHPUnit_Framework_TestCase {
    public function testGuessMode() {
        $filter = new CriteriaFilter('foo', 'bar', '=');
        $operator = new Foo($filter);

        $this->assertEquals("foo", $operator->guessField("foo"));
        $this->assertEquals("foo", $operator->guessField("foo.bar")->relationName);
        $this->assertEquals("bar", $operator->guessField("foo.bar")->fieldName);

        try {
            $operator->guessField("foo.bar.");
            $this->fail();
        } catch (\Exception $ex) {}

        try {
            $operator->guessField("foo.bar.baz");
            $this->fail();
        } catch (\Exception $ex) {}

        try {
            $operator->guessField(".foo");
            $this->fail();
        } catch (\Exception $ex) {}

        try {
            $operator->guessField(".");
            $this->fail();
        } catch (\Exception $ex) {}

        try {
            $operator->guessField("");
            $this->fail();
        } catch (\Exception $ex) {}
    }
}

// Stub.
class Foo extends AbstractOperator {

    public function apply($query) {}

}