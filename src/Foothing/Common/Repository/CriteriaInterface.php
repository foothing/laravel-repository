<?php namespace Foothing\Common\Repository;

use Foothing\Common\Request\AbstractRemoteQuery;

interface CriteriaInterface {

	function make(AbstractRemoteQuery $query);

	function filter($field, $value, $operator = '=');

	function resetFilters();

	function order($field, $sort = 'asc');

	function sort($direction);

	function applyOrderBy($queryBuilder);

	function applyFilters($queryBuilder);
}