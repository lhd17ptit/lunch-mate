<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all
     * @return mixed
     */
    public function getAll();

    public function getAllActive();

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = []);

    /**
     * Update
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = []);

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);


    /**
     * findByField
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findByField($field, $value);

    /**
     * findByFields
     * @param $conditions
     * @return mixed
     */
    public function findByFields($conditions);

    /**
     * getWithFields
     * @param $conditions
     * @return mixed
     */
    public function getWithFields($fields);

    /**
     * findMany
     * @param $ids
     * @return mixed
     */
    public function findMany($ids = []);
}
