<?php

namespace TodoExample\Storage;

interface Storage
{
    /**
     * @param object $object
     * @return string (ID)
     */
    public function store($object);

    /**
     * @return array indexed by object's IDs
     */
    public function all();

    /**
     * @param string $id
     * @return object|null
     */
    public function retrieve($id);
}
