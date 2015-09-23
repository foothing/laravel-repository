<?php namespace Foothing\Common\Request;

abstract class AbstractRemoteQuery {
	public $sortField;
	public $sortDirection = 'asc';
	public $sortEnabled = false;
	public $filters;

	public $filterEnabled = false;

	// @TODO php 5.4 doesnt allow abstract static
	public /*abstract*/ static function spawn($input) {}
}