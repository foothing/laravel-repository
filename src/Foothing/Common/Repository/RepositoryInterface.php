<?php
namespace Foothing\Common\Repository;

use Foothing\Common\Request\AbstractRemoteQuery;

interface RepositoryInterface {

	//
	//
	//	Crud.
	//
	//

	function find($id);
	function all();
	function paginate(AbstractRemoteQuery $params = null, $limit = null, $offset = null);

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