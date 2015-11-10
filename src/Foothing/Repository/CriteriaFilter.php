<?php namespace Foothing\Repository;

use Foothing\Repository\Operators\OperatorFactory;

class CriteriaFilter {
	public $field;
	public $value;
	public $operator;

	public function __construct($field = null, $value = null, $operator = '=') {
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
	}

    public function apply($queryBuilder) {
        return OperatorFactory::make($this)->apply($queryBuilder);
    }
}
