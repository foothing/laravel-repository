<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\RepositoryInterface;

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

	function findAdvanced($params = null, $limit = null, $offset = null) {
		// Prototype for filtered and ordered item list.
		// Default implementation returns findAll().
		return $this->findAll($limit, $offset);
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
}