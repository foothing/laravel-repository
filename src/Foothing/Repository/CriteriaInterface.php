<?php namespace Foothing\Repository;

use Foothing\Request\AbstractRemoteQuery;

interface CriteriaInterface {

    public function make(AbstractRemoteQuery $query);

    /**
     * Set a filter on the given field.
     * Allowed operators: '=', 'like', '<', '<=', '>', '>=', '!=', 'in'
     *
     * @param        $field
     * @param        $value
     * @param string $operator
     *
     * @return mixed
     */
    public function filter($field, $value, $operator = '=');

    /**
     * Set the given CriteriaFilter.
     *
     * @param CriteriaFilter $filter
     *
     * @return mixed
     */
    public function setFilter(CriteriaFilter $filter);

    /**
     * Reset all filters.
     * @return mixed
     */
    public function resetFilters();

    /**
     * Reset order clause.
     * @return mixed
     */
    public function resetOrder();

    /**
     * Reset filters and order clause.
     * @return mixed
     */
    public function reset();

    /**
     * Set fetch order by.
     * @param        $field
     * @param string $sort
     *
     * @return mixed
     */
    public function order($field, $sort = 'asc');

    /**
     * Set order by direction.
     * @param $direction
     *
     * @return mixed
     */
    public function sort($direction);

    /**
     * Applies orderBy to the given query builder.
     * @param $queryBuilder
     *
     * @return mixed
     */
    public function applyOrderBy($queryBuilder);

    /**
     * Applies filters to the given query builder.
     * @param $queryBuilder
     *
     * @return mixed
     */
    public function applyFilters($queryBuilder);
}
