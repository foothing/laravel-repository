<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\Criteria;
use Foothing\Common\Repository\CriteriaInterface;
use Foothing\Common\Repository\RepositoryInterface;
use Foothing\Common\Resources\ResourceInterface;

abstract class AbstractEloquentRepository implements RepositoryInterface {
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

	function __construct(\Illuminate\Database\Eloquent\Model $model) {
		$this->model = $model;
		$this->criteria = new EloquentCriteria();
		if ( $this->model instanceof ResourceInterface ) {
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
		return $result;
	}

	/**
	 * Check whether the autoEagerLoading feature has to be applied.
	 *
	 * @param string $fetch
	 */
	protected function applyAutoEagerLoading($fetch = 'unit') {
		if ( ! empty($this->eagerLoad) ) {
			return;
		}

		if ( $this->enableAutoEagerLoading && $fetch == 'unit') {
			$this->eagerLoad = $this->model->unitRelations();
		}

		else if ( $this->enableAutoEagerLoading && $fetch == 'list') {
			$this->eagerLoad = $this->model->listRelations();
		}
	}

	function find($id) {
		$this->applyAutoEagerLoading('unit');
		return $this->finalize( $this->model->with( $this->eagerLoad )->find($id) );
	}

	function findOneBy($field, $arg1, $arg2 = null) {
		$this->criteria->filter($field, $arg1, $arg2);
		$queryBuilder = $this->criteria->applyFilters($this->model);
		$this->applyAutoEagerLoading('unit');
		return $this->finalize( $queryBuilder->with( $this->eagerLoad )->first() );
	}

	function findAllBy($field, $arg1, $arg2 = null) {
		return $this->filter($field, $arg1, $arg2)->all();
	}

	function all() {
		$queryBuilder = $this->criteria->applyFilters($this->model);
		$queryBuilder = $this->criteria->applyOrderBy($queryBuilder);
		$this->applyAutoEagerLoading('list');
		return $this->finalize( $queryBuilder->with( $this->eagerLoad )->get() );
	}

	function paginate($limit = null, $offset = null) {
		$queryBuilder = $this->criteria->applyOrderBy($this->model);
		$queryBuilder = $this->criteria->applyFilters($queryBuilder);
		$this->applyAutoEagerLoading('list');
		return $this->finalize( $queryBuilder->with( $this->eagerLoad )->paginate($limit) );
	}

	function create($entity) {
		$this->applyAutoEagerLoading('unit');

		if ($entity->save()) {
			if ( $this->eagerLoad ) {
				$entity->load( $this->eagerLoad );
			}
			return $this->finalize( $entity );
		} else {
			throw new \Exception("Cannot create this entity");
		}
	}

	function update($entity) {
		if ( ! $entity->exists ) {
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
			if ( $this->eagerLoad ) {
				$entity->load( $this->eagerLoad );
			}
			return $this->finalize( $entity );
		} else {
			throw new \Exception("Cannot create this entity");
		}
	}

	function delete($entity) {
		return $entity->delete();
	}

	function with(array $relations) {
		$this->eagerLoad = $relations;
		return $this;
	}

	function filter($field, $value, $operator = '=') {
		$this->criteria->filter($field, $value, $operator);
		return $this;
	}

	function order($field) {
		$this->criteria->order($field);
		return $this;
	}

	function sort($direction) {
		$this->criteria->sort($direction);
		return $this;
	}

	function refresh() {
		$this->refreshFlag = true;
		return $this;
	}

	function reset() {
		$this->refreshFlag = false;
		return $this;
	}

	function validationRules() { return []; }

	function validationRulesPartial($partial) {
		$rules = $this->validationRules();
		if ( empty($partial) ) {
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