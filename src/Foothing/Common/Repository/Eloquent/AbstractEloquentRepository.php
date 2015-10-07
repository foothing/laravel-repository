<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\Criteria;
use Foothing\Common\Repository\CriteriaInterface;
use Foothing\Common\Repository\RepositoryInterface;
use Foothing\Common\Request\AbstractRemoteQuery;

abstract class AbstractEloquentRepository implements RepositoryInterface {
	protected $model;
	protected $eagerLoad = [];

	function __construct(\Illuminate\Database\Eloquent\Model $model) {
		$this->model = $model;
	}

	protected function finalize($result) {
		$this->eagerLoad = [];
		return $result;
	}

	function find($id) {
		return $this->finalize( $this->model->with( $this->eagerLoad )->find($id) );
	}

	function all() {
		return $this->finalize( $this->model->with( $this->eagerLoad )->get() );
	}

	function paginate(CriteriaInterface $criteria = null, $limit = null, $offset = null) {
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