<?php namespace Foothing\Repository\Tests\Fixtures;

class Person extends \Illuminate\Database\Eloquent\Model implements \Foothing\Resources\ResourceInterface {
    protected $table = 'person';
    public $timestamps = false;

    public function roles() {
        return $this->belongsToMany('Foothing\Repository\Tests\Fixtures\Role', 'person_roles', 'pid', 'rid');
    }

    public function children() {
        return $this->hasMany('Son', 'pid');
    }

    public function unitRelations() {
        return ['roles', 'children'];
    }

    public function listRelations() {
        return ['roles'];
    }

    public function skipOnSave() {
        return ['roles', 'children'];
    }

    public function scopeMale($query) {
        return $query->whereIn('id', [1, 3]);
    }
}
