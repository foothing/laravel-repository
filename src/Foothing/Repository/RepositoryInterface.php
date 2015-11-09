<?php
namespace Foothing\Repository;

use Foothing\Request\AbstractRemoteQuery;

interface RepositoryInterface {

    //
    //
    //	Crud.
    //
    //

    public function find($id);
    public function findOneBy($field, $arg1, $arg2 = null);
    public function findAllBy($field, $arg1, $arg2 = null);
    public function all();
    public function paginate($limit = null, $offset = null);

    public function create($entity);
    public function update($entity);
    public function delete($entity);

    //
    //
    //	Eager loading.
    //
    //

    public function with(array $relations);

    //
    //
    //	Criteria shortcuts.
    //
    //

    public function criteria(CriteriaInterface $criteria);

    public function filter($field, $value, $operator = '=');

    public function order($field, $sort = null);

    public function sort($direction);

    //
    //
    //	Helpers.
    //
    //

    /**
     * Forces the next read query to skip cached values.
     * @return self
     */
    public function refresh();

    /**
     * Reset the refresh flag.
     * @return self
     */
    public function reset();

    public function validationRules();

    public function validationRulesPartial($partial);
}
