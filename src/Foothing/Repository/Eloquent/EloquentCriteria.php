<?php namespace Foothing\Repository\Eloquent;

use Foothing\Repository\CriteriaFilter;
use Foothing\Repository\CriteriaInterface;
use Foothing\Repository\Operators\Factory;
use Foothing\Request\AbstractRemoteQuery;

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

    public function filter($field, $value, $operator = null) {
        if (! $operator) {
            $operator = '=';
        }

        return $this->setFilter(new CriteriaFilter($field, $value, $operator));
    }

    public function setFilter(CriteriaFilter $filter) {
        $this->filters[] = $filter;
        return $this;
    }

    public function resetFilters() {
        $this->filters = [];
        return $this;
    }

    public function resetOrder() {
        $this->orderBy = null;
        $this->sortDirection = 'asc';
        return $this;
    }

    public function reset() {
        return $this->resetFilters()->resetOrder();
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
        if ($this->orderBy) {
            $queryBuilder = $queryBuilder->orderBy($this->orderBy, $this->sortDirection);
        }
        return $queryBuilder;
    }

    public function applyFilters($queryBuilder) {
        foreach ($this->filters as $filter) {
            $queryBuilder = $filter->apply($queryBuilder);
        }
        return $queryBuilder;
    }
}
