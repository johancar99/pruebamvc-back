<?php

namespace App\Interfaces;

interface RepositoryInterface
{
    /**
     * Retrieve all records.
     *
     * @return mixed
     */
    public function all();

    /**
     * Retrieve all active records.
     *
     * @return mixed
     */
    public function allActive();

    /**
     * Clears the current query.
     *
     * @return self
     */
    public function clearQuery();

    /**
     * Fills the model from an array of data.
     *
     * @param array $data
     * @return self
     */
    public function fillFromArray(array $data);

    /**
     * Filters records by created date range.
     *
     * @param mixed $start_date
     * @param mixed $end_date
     * @return mixed
     */
    public function filter_by_created_at($start_date, $end_date= null);

    /**
     * Filters records by a specific column and its value.
     *
     * @param string $column
     * @param mixed $value
     * @return mixed
     */
    public function filter_by_column($column, $value);

    /**
     * Find a record by its ID.
     *
     * @param int $id
     * @return mixed
     */
    public function find($id);

    /**
     * Find a record and return it along with other related data.
     *
     * @param int $id
     * @return mixed
     */
    public function findAndGet($id);

    /**
     * Get the associated model instance.
     *
     * @return mixed
     */
    public function getModel();

    /**
     * Create a new query instance.
     *
     * @return mixed
     */
    public function newQuery();

    /**
     * Order records by a specific column.
     *
     * @param string $field
     * @param string $order
     * @return mixed
     */
    public function orderByColumn($field, $order= 'asc');

    /**
     * Paginate the results.
     *
     * @param int $recordsByPage
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    public function paginate($recordsByPage = 20, $orderBy = 'created_at', $order = 'desc');

    /**
     * Pre-create logic before saving a record.
     *
     * @return void
     */
    public function preCreate();

    /**
     * Pre-delete logic before removing a record.
     *
     * @return void
     */
    public function preDelete();

    /**
     * Pre-update logic before updating a record.
     *
     * @return void
     */
    public function preUpdate();

    /**
     * Access the current query instance.
     *
     * @return mixed
     */
    public function query();

    /**
     * Set the model for the repository.
     *
     * @param mixed $model
     * @return self
     */
    public function setModel($model);

    /**
     * Set the user associated with the repository.
     *
     * @param mixed $user
     * @return self
     */
    public function setUser($user);

    /**
     * Apply a where clause to the query.
     *
     * @param string $column
     * @param mixed $value
     * @return self
     */
    public function where($column, $value);

}
