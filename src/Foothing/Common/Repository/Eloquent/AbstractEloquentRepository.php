<?php
namespace Foothing\Common\Repository\Eloquent;

use Foothing\Common\Repository\RepositoryInterface;

abstract class AbstractEloquentRepository implements RepositoryInterface {
	protected $model;
	protected $refreshFlag;

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

	function findBy($attribute, $value, $operator = '=') {
		return $this->model->where($attribute, $operator, $value)->get();
	}

	function findOneBy($attribute, $value, $operator = '=') {
		return $this->model->where($attribute, $operator, $value)->first();
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
}