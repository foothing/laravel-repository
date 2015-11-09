<?php namespace Foothing\Repository;

use Foothing\Request\AbstractRemoteQuery;

interface CriteriaInterface {

    public function make(AbstractRemoteQuery $query);

    public function filter($field, $value, $operator = '=');

    public function resetFilters();

    public function resetOrder();

    public function reset();

    public function order($field, $sort = 'asc');

    public function sort($direction);

    public function applyOrderBy($queryBuilder);

    public function applyFilters($queryBuilder);
}
