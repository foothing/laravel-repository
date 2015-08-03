<?php
namespace Foothing\Common\Repository;

interface RepositoryInterface {
	function find($id);
	function all();
	function findAll($limit = null, $offset = null);
	function findAdvanced($params = null, $limit = null, $offset = null);

	function create($entity);
	function update($entity);
	function delete($entity);
}