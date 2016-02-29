<?php namespace Foothing\Repository\Operators;

class DefaultOperator extends AbstractOperator {

    public function apply($query) {
        return $query->where($this->filter->field, $this->filter->operator, $this->filter->value);
    }
}
