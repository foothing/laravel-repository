<?php

class Person extends \Illuminate\Database\Eloquent\Model implements \Foothing\Resources\ResourceInterface {
	protected $table = 'person';
	public $timestamps = false;

	public function roles() {
		return $this->belongsToMany('Role', 'person_roles', 'pid', 'rid');
	}

	public function children() {
		return $this->hasMany('Son', 'pid');
	}

	function unitRelations() {
		return ['roles', 'children'];
	}

	function listRelations() {
		return ['roles'];
	}

	function skipOnSave() {
		return ['roles', 'children'];
	}
}