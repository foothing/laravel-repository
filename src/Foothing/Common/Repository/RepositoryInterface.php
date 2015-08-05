<?php
namespace Foothing\Common\Repository;

interface RepositoryInterface {

	//
	//
	//	Basic crud.
	//
	//

	function find($id);
	function all();
	function findAll($limit = null, $offset = null);
	function findAdvanced($params = null, $limit = null, $offset = null);
	function findBy($attribute, $value, $operator = '=');
	function findOneBy($attribute, $value, $operator = '=');
	function create($entity);
	function update($entity);
	function delete($entity);

	//
	//
	//	Helpers.
	//
	//

	/**
	 * Forces the next read query to skip cached values.
	 * @return self
	 */
	function refresh();

	/**
	 * Reset the refresh flag.
	 * @return self
	 */
	function reset();
}