<?php namespace Foothing\Repository\Operators;

use Foothing\Repository\CriteriaFilter;

abstract class AbstractOperator {
    protected $filter;

    public function __construct(CriteriaFilter $filter) {
        $this->filter = $filter;
    }

    public abstract function apply($query);
}
