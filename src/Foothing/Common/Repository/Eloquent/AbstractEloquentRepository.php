<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\Criteria;
use Foothing\Common\Repository\CriteriaInterface;
use Foothing\Common\Repository\RepositoryInterface;
use Foothing\Common\Resources\ResourceInterface;

abstract class AbstractEloquentRepository implements RepositoryInterface {
	protected $model;

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

	function __construct(\Illuminate\Database\Eloquent\Model $model) {
		$this->model = $model;
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
		$criteria = new EloquentCriteria();
		$criteria->filter($field, $arg1, $arg2);
		$queryBuilder = $criteria->applyFilters($this->model);
		$this->applyAutoEagerLoading('unit');
		return $this->finalize( $queryBuilder->with( $this->eagerLoad )->first() );
	}

	function findAllBy($field, $arg1, $arg2 = null) {
		$criteria = new EloquentCriteria();
		$criteria->filter($field, $arg1, $arg2);
		$queryBuilder = $criteria->applyFilters($this->model);
		$this->applyAutoEagerLoading('list');
		return $this->finalize( $queryBuilder->with( $this->eagerLoad )->get() );
	}

	function all() {
		$this->applyAutoEagerLoading('list');
		return $this->finalize( $this->model->with( $this->eagerLoad )->get() );
	}

	function paginate(CriteriaInterface $criteria = null, $limit = null, $offset = null) {
		$this->applyAutoEagerLoading('list');

		// Check if we have input parameters.
		if ($criteria) {
			$queryBuilder = $this->model;

			// Apply sorting criteria.
			$queryBuilder = $criteria->applyOrderBy($queryBuilder);

			// Apply filters.
			$queryBuilder = $criteria->applyFilters($queryBuilder);

			return $this->finalize( $queryBuilder->with( $this->eagerLoad )->paginate($limit) );
		}

		else {
			return $this->finalize( $this->model->with( $this->eagerLoad )->paginate($limit) );
		}
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