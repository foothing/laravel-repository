<?php
namespace Foothing\Repository;

use Foothing\Request\AbstractRemoteQuery;

interface RepositoryInterface {

	//
	//
	//	Basic crud.
	//
	//

	function find($id);
	function findOneBy($field, $arg1, $arg2 = null);
	function findAllBy($field, $arg1, $arg2 = null);
	function all();
	function paginate($limit = null, $offset = null);

	function create($entity);
	function update($entity);
	function delete($entity);

	//
	//
	//	Eager loading.
	//
	//

	function with(array $relations);

	//
	//
	//	Criteria shortcuts.
	//
	//

	function criteria(CriteriaInterface $criteria);

	function filter($field, $value, $operator = '=');

	function order($field);

	function sort($direction);

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

	function validationRules();

	function validationRulesPartial($partial);

}