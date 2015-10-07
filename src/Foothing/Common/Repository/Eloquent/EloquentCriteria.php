<?php namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\CriteriaFilter;
use Foothing\Common\Repository\CriteriaInterface;
use Foothing\Common\Request\AbstractRemoteQuery;

class EloquentCriteria implements CriteriaInterface {
	public $orderBy;
	public $sortDirection = 'asc';
	public $filters = [];
	protected $allowedOperators = ['=', 'like', '<', '<=', '>', '>=', '!='];

	public function make(AbstractRemoteQuery $abstractRemoteQuery) {
		if ($abstractRemoteQuery->filterEnabled) {
			foreach ($abstractRemoteQuery->filters as $filter) {
				$this->filter($filter->name, $filter->value, $filter->operator);
			}
		}

		if ($abstractRemoteQuery->sortEnabled) {
			$this->order($abstractRemoteQuery->sortField, $abstractRemoteQuery->sortDirection);
		}

		return $this;
	}

	public function filter($field, $value, $operator = '=') {
		if ( ! $operator ) {
			$operator = '=';
		}

		if ( ! $this->validateOperator($operator) ) {
			throw new \Exception("Unallowed operator: $operator");
		}

		$filter = new CriteriaFilter();
		$filter->field = $field;
		$filter->value = $value;
		// @TODO 'like' handler
		$filter->operator = $operator;
		$this->filters[] = $filter;
		return $this;
	}

	protected function validateOperator($operator) {
		return in_array($operator, $this->allowedOperators);
	}

	public function resetFilters() {
		$this->filters = [];
		return $this;
	}

	public function order($field, $sort = 'asc') {
		$this->orderBy = $field;
		$this->sort($sort);
		return $this;
	}

	public function sort($direction) {
		if ($direction != 'asc' && $direction != 'desc') {
			$direction = 'asc';
		}
		$this->sortDirection = $direction;
		return $this;
	}

	public function applyOrderBy($queryBuilder) {
		if ( $this->orderBy ) {
			$queryBuilder = $queryBuilder->orderBy($this->orderBy, $this->sortDirection);
		}
		return $queryBuilder;
	}

	public function applyFilters($queryBuilder) {
		foreach ($this->filters as $filter) {
			$queryBuilder = $queryBuilder->where($filter->field, $filter->operator, $filter->value);
		}
		return $queryBuilder;
	}
}