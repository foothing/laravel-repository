<?php

class PersonRepository extends \Foothing\Repository\Eloquent\EloquentRepository {

    protected $globalScope = "funny";

    public function __construct(\Foothing\Repository\Tests\Fixtures\Person $model) {
        parent::__construct($model);
    }

}