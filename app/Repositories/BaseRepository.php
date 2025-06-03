<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function query()
    {
        return $this->model->query();
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllExcept($excludedId)
    {
        return $this->model->whereNotIn('id', (array) $excludedId)->get();
    }

    public function getAllWithConditions(array $conditions)
    {
        $query = $this->model->query();

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    public function getAllActive()
    {
        return $this->model->where('active', ACTIVE)->get();
    }

    public function getAllByStatus($status)
    {
        return $this->model->whereStatus($status)->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findOrFailWithActive($id)
    {
        return $this->model->where('active', ACTIVE)->findOrFail($id);
    }

    public function findOrNew($id)
    {
        return $this->model->findOrNew($id);
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function deleteByIds($ids)
    {
        if (is_array($ids)) {
            // Delete multiple records
            return $this->model->destroy($ids);
        }

        // Delete single record
        return $this->model->destroy([$ids]);
    }

    public function findByField($field, $value)
    {
        return $this->model->where($field, $value);
    }

    public function findByFields($conditions)
    {
        return $this->model->where($conditions);
    }

    public function whereIn($field, $value)
    {
        return $this->model->whereIn($field, $value);
    }

    public function whereNotIn($field, $value)
    {
        return $this->model->whereNotIn($field, $value);
    }

    public function createMultiple($attributes = [])
    {
        return $this->model->insert($attributes);
    }

    public function whereColumn($field, $value)
    {
        return $this->model->whereColumn($field, $value);
    }

    public function getWithFields($fields = [])
    {
        return $this->model->select($fields);
    }

    public function findMany($ids = [])
    {
        return $this->model->whereIn('id', $ids);
    }

    public function updateOrCreate($conditions, $value)
    {
        return $this->model->UpdateOrCreate($conditions, $value);
    }

    public function firstOrCreate($attributes = [])
    {
        return $this->model->firstOrCreate($attributes);
    }

    public function join($table, $first, $operator, $second, $type = 'inner')
    {
        return $this->model->join($table, $first, $operator, $second, $type);
    }
}
