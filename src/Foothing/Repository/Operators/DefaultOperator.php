<?php namespace Foothing\Repository\Operators;

class DefaultOperator extends AbstractOperator {

    public function apply($query) {
        $field = $this->field;

        if ($field instanceof NestedField) {
            return $query->whereHas($field->relationName, function($subquery) use($field) {
                $subquery->where($field->fieldName, $this->filter->operator, $this->filter->value);
            });
        }

        else {
            return $query->where($field, $this->filter->operator, $this->filter->value);
        }
    }
}
