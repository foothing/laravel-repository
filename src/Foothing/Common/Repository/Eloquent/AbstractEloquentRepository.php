<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\RepositoryInterface;
use Foothing\Common\Request\AbstractRemoteQuery;

abstract class AbstractEloquentRepository implements RepositoryInterface {
	protected $model;

	function __construct(\Illuminate\Database\Eloquent\Model $model) {
		$this->model = $model;
	}

	function find($id) {
		return $this->model->find($id);
	}

	function all() {
		return $this->model->all();
	}

	function findAll($limit = null, $offset = null) {
		return $this->model->paginate($limit);
	}

	function findAdvanced(AbstractRemoteQuery $params = null, $limit = null, $offset = null) {
		// Check if we have input parameters.
		if ($params) {
			$queryBuilder = $this->model;

			// Apply sorting criteria.
			if ( $params->sortEnabled ) {
				$queryBuilder = $this->model->orderBy($params->sortField, $params->sortDirection);
			}

			// Apply filters.
			if ( $params->filterEnabled ) {
				foreach ($params->filters as $filter) {
					if ($filter->operator == 'like') {
						$filter->value = "%{$filter->value}%";
					}
					$queryBuilder = $queryBuilder->where($filter->name, $filter->operator, $filter->value);
				}
			}

			return $queryBuilder->paginate($limit);
		}

		else {
			return $this->findAll($limit, $offset);
		}
	}

	function create($entity) {
		if ($entity->save()) {
			return $entity;
		} else {
			throw new \Exception("Cannot create this entity");
		}
	}

	function update($entity) {
		return $this->create($entity);
	}

	function delete($entity) {
		return $entity->delete();
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

	function validate() { return true; }

}