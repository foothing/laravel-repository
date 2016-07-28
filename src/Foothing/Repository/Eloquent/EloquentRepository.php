<?php
namespace Foothing\Repository\Eloquent;

use Foothing\Repository\Criteria;
use Foothing\Repository\CriteriaInterface;
use Foothing\Repository\RepositoryInterface;
use Foothing\Resources\ResourceInterface;

class EloquentRepository implements RepositoryInterface {

    /**
     * The Eloquent Model.
     * @var Model
     */
    protected $model;

    protected $refreshFlag;

    /**
     * @var array
     * Array of relations we want to eager load.
     */
    protected $eagerLoad = [];

    /**
     * @var bool
     * If the model implements ResourceInterface we use
     * its setting to decide whether eager load relations.
     * Note that this behaviour will be overwritten upon
     * explicit with() invocations.
     */
    protected $enableAutoEagerLoading = false;

    /**
     * @var EloquentCriteria
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $scope;

    public function __construct(\Illuminate\Database\Eloquent\Model $model) {
        $this->setModel($model);
        $this->criteria = new EloquentCriteria();
    }

    /**
     * Set the repository model.
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function setModel(\Illuminate\Database\Eloquent\Model $model) {
        $this->model = $model;
        if ($this->model instanceof ResourceInterface) {
            $this->enableAutoEagerLoading = true;
        }
    }

    /**
     * Utility method for flags reset. Always use this to return results.
     *
     * @param $result
     * The result we want to return
     *
     * @return mixed
     */
    protected function finalize($result) {
        $this->eagerLoad = [];
        $this->criteria->reset();
        $this->scope = null;
        return $result;
    }

    /**
     * Check whether the autoEagerLoading feature has to be applied.
     *
     * @param string $fetch
     */
    protected function applyAutoEagerLoading($fetch = 'unit') {
        if (! empty($this->eagerLoad)) {
            return;
        }

        if ($this->enableAutoEagerLoading && $fetch == 'unit') {
            $this->eagerLoad = $this->model->unitRelations();
        }

        elseif ($this->enableAutoEagerLoading && $fetch == 'list') {
            $this->eagerLoad = $this->model->listRelations();
        }
    }

    public function find($id) {
        $this->applyAutoEagerLoading('unit');
        return $this->finalize($this->applyScope()->with($this->eagerLoad)->find($id));
    }

    public function findOneBy($field, $arg1, $arg2 = null) {
        $this->criteria->filter($field, $arg1, $arg2);
        $queryBuilder = $this->applyScope();
        $queryBuilder = $this->criteria->applyFilters($queryBuilder);
        $this->applyAutoEagerLoading('unit');
        return $this->finalize($queryBuilder->with($this->eagerLoad)->first());
    }

    public function findAllBy($field, $arg1, $arg2 = null) {
        return $this->filter($field, $arg1, $arg2)->all();
    }

    public function all() {
        $queryBuilder = $this->applyScope();
        $queryBuilder = $this->criteria->applyFilters($queryBuilder);
        $queryBuilder = $this->criteria->applyOrderBy($queryBuilder);
        $this->applyAutoEagerLoading('list');
        return $this->finalize($queryBuilder->with($this->eagerLoad)->get());
    }

    public function paginate($limit = null, $offset = null) {
        $queryBuilder = $this->applyScope();
        $queryBuilder = $this->criteria->applyOrderBy($queryBuilder);
        $queryBuilder = $this->criteria->applyFilters($queryBuilder);
        $this->applyAutoEagerLoading('list');
        return $this->finalize($queryBuilder->with($this->eagerLoad)->paginate($limit));
    }

    public function create($entity) {
        $this->applyAutoEagerLoading('unit');

        // Skip relation fields.
        if ($entity instanceof ResourceInterface) {
            foreach ($entity->skipOnSave() as $relation) {
                unset($entity->{$relation});
            }
        }

        if ($entity->save()) {
            if ($this->eagerLoad) {
                $entity->load($this->eagerLoad);
            }
            return $this->finalize($entity);
        }

        throw new \Exception("Cannot create this entity");
    }

    public function update($entity) {
        if (! $entity->exists) {
            throw new \Exception("Cannot update entity that doesn't exists");
        }

        $this->applyAutoEagerLoading('unit');

        // Since input may come from a JSON request, we
        // can happen to find relations populated with
        // stdClass object, which would break Eloquent
        // save. Make sure they are unset before update.
        if ($entity instanceof ResourceInterface) {
            foreach ($entity->skipOnSave() as $relation) {
                unset($entity->{$relation});
            }
        }

        if ($entity->save()) {
            if ($this->eagerLoad) {
                $entity->load($this->eagerLoad);
            }
            return $this->finalize($entity);
        }

        throw new \Exception("Cannot create this entity");
    }

    public function delete($entity) {
        return $entity->delete();
    }

    public function with(array $relations) {
        $this->eagerLoad = $relations;
        return $this;
    }

    public function attach($entity, $relation, $relatedEntity) {
        $modelRelation = $entity->{$relation}();

        if (! $modelRelation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
            throw new \Exception("Can't attach if relation is not many-to-many.");
        }

        $modelRelation->attach($relatedEntity->id);
        return $this->finalize($entity);
    }

    public function detach($entity, $relation, $relatedEntity) {
        $modelRelation = $entity->{$relation}();

        if (! $modelRelation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
            throw new \Exception("Can't detach if relation is not many-to-many.");
        }

        $modelRelation->detach($relatedEntity->id);
        return $this->finalize($entity);
    }

    public function filter($field, $value, $operator = '=') {
        $this->criteria->filter($field, $value, $operator);
        return $this;
    }

    public function order($field, $sort = null) {
        $this->criteria->order($field);
        if ($sort) {
            $this->criteria->sort($sort);
        }
        return $this;
    }

    public function sort($direction) {
        $this->criteria->sort($direction);
        return $this;
    }

    public function criteria(CriteriaInterface $criteria) {
        $this->criteria = $criteria;
        return $this;
    }

    public function scope($scope) {
        $this->scope = $scope;
        return $this;
    }

    protected function applyScope() {
        if (! $this->scope) {
            return $this->model;
        }

        $queryBuilder = $this->model->{$this->scope}();

        if (! $queryBuilder instanceof \Illuminate\Database\Eloquent\Builder) {
            throw new \Exception("Scope {$this->scope} is not valid.");
        }

        return $queryBuilder;
    }

    public function refresh() {
        $this->refreshFlag = true;
        return $this;
    }

    public function reset() {
        $this->refreshFlag = false;
        return $this;
    }

    public function validationRules() {
        return [];
    }

    public function validationRulesPartial($partial) {
        $rules = $this->validationRules();
        if (empty($partial)) {
            return $rules;
        }

        $partial = array($partial);
        $result = [];
        foreach ($partial as $field) {
            $result[ $field ] = $rules[$field];
        }
        return $result;
    }
}
