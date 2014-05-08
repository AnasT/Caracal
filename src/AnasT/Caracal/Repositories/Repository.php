<?php namespace AnasT\Caracal\Repositories;

interface Repository {

    /**
     * Retrieve all entities.
     *
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns);

}