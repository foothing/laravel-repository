<?php namespace Foothing\Repository;

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
    //	Relations.
    //
    //

    /**
     * Attach $relatedEntity and $entity in a many-to-many relation.
     *
     * @param Model $entity
     * @param string $relation
     * @param Model $relatedEntity
     *
     * @return Model the updated $entity
     */
    public function attach($entity, $relation, $relatedEntity);

    /**
     * Detach $entity and $relatedEntity in a many-to-many relation.
     *
     * @param Model $entity
     * @param string $relation
     * @param Model $relatedEntity
     *
     * @return Model the updated $entity
     */
    public function detach($entity, $relation, $relatedEntity);

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
