<?php namespace Foothing\Resources;

interface ResourceInterface {
    /**
     * Array of relations we want to eager-load when
     * a single entity is being fetched.
     *
     * @return array
     */
    public function unitRelations();

    /**
     * Array of relations we want to eager-load when
     * a list of entities is being fetched.
     *
     * @return array
     */
    public function listRelations();

    /**
     * When the resource is sent in a json-encoded
     * format it may happen to have relations fields
     * populated. Since they would be set as stdClass
     * objects we need to unset them before save.
     *
     * This method should return an array with all relations
     * we want to be unset when processing the updates.
     *
     * @return array
     */
    public function skipOnSave();

    // @TODO: validation.
}
