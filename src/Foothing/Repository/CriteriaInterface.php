<?php namespace Foothing\Repository;

use Foothing\Request\AbstractRemoteQuery;

interface CriteriaInterface {

	function make(AbstractRemoteQuery $query);

	function filter($field, $value, $operator = '=');

	function resetFilters();

	function resetOrder();

	function reset();

	function order($field, $sort = 'asc');

	function sort($direction);

	function applyOrderBy($queryBuilder);

	function applyFilters($queryBuilder);
}