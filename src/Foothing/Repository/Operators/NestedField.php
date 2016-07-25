<?php namespace Foothing\Repository\Operators;

class NestedField {
    public $relationName;
    public $fieldName;

    public function __construct($relationName, $fieldName) {
        $this->relationName = $relationName;
        $this->fieldName = $fieldName;
    }
}
