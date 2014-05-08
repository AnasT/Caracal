<?php namespace AnasT\Caracal\Repositories;

interface Paginable {

    /**
     * Paginate Results.
     *
     * @param int $amount
     * @param array $columns
     * @return Illuminate\Pagination\Paginator
     */
    public function paginate($amount, array $columns);

}