<?php
namespace Foothing\Common\Repository;

use Foothing\Common\Request\AbstractRemoteQuery;

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
	function paginate(CriteriaInterface $params = null, $limit = null, $offset = null);

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