<?php

class Person extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'person';
	public $timestamps = false;

	public function roles() {
		return $this->belongsToMany('Role', 'person_roles', 'pid', 'rid');
	}

	public function children() {
		return $this->hasMany('Son', 'pid');
	}

}