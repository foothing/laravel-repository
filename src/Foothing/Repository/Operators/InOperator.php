<?php namespace Foothing\Repository\Operators;

class InOperator extends DefaultOperator {

    public function apply($query) {
        if (! is_array($this->filter->value)) {
            $value = explode(",", $this->filter->value);
        } else {
            $value = $this->filter->value;
        }

        return $query->whereIn($this->filter->field, $value);
    }
}
