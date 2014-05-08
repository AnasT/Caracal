<?php namespace AnasT\Caracal\Repositories;

use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {

    /**
     * Laravel application.
     *
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The Eloquent model.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new instance of AbstractRepository.
     *
     * @param Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app, Model $model)
    {
        $this->app = $app;
        $this->model = $model;
    }

    /**
     * Returns the model.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Retrieve all entities.
     *
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = array('*'))
    {
        return $this->model->all($columns);
    }

    /**
     * Find a single entity.
     *
     * @param int $id
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Paginate results.
     *
     * @param int $amount
     * @param array $columns
     * @return Illuminate\Pagination\Paginator
     */
    public function paginate($amount, array $columns = array('*'))
    {
        return $this->model->paginate($amount, $columns);
    }

}