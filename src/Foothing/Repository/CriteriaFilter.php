<?php namespace Foothing\Repository;

class CriteriaFilter {
	public $field;
	public $value;
	public $operator;

	function __construct($field = null, $value = null, $operator = '=') {
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
	}
}