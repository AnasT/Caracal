<?php namespace AnasT\Caracal\Repositories;

interface Crudable {

  /**
   * Find a single entity.
   *
   * @param int $id
   * @param array $columns
   * @return Illuminate\Database\Eloquent\Model
   */
  public function find($id, array $columns);

  /**
   * Create a new entity.
   *
   * @param array $attributes
   * @return mixed
   */
  public function create(array $attributes);

  /**
   * Update an existing entity.
   *
   * @param int $id
   * @param array $attributes
   * @return boolean
   */
  public function update($id, array $attributes);

  /**
   * Delete an existing entity.
   *
   * @param int $id
   * @return boolean
   */
  public function delete($id);

}