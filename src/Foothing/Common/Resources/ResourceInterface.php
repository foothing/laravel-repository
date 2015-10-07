<?php namespace Foothing\Common\Resources;

interface ResourceInterface {
	/**
	 * Array of relations we want to eager-load when
	 * a single entity is being fetched.
	 *
	 * @return array
	 */
	function unitRelations();

	/**
	 * Array of relations we want to eager-load when
	 * a list of entities is being fetched.
	 *
	 * @return array
	 */
	function listRelations();
}