<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class Repository implements RepositoryInterface
{
    /**
     * The model instance associated with the repository.
     *
     * @var mixed
     */
    protected $model;

    /**
     * The user instance associated with the repository.
     *
     * @var mixed|null
     */
    protected $user = null;

    /**
     * The query instance for the repository.
     *
     * @var mixed|null
     */
    protected $query = null;

    /**
     * Get a new instance of the model.
     *
     * @return mixed
     */
    abstract public function getNewModel();

    /**
     * Constructor to set the model for the repository.
     *
     * @param mixed $model
     */
    public function __construct($model = null)
    {
        $this->setModel($model);
    }

    /**
     * Retrieve all records from the model.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->getNewModel()->all();
    }

    /**
     * Retrieve all active records.
     *
     * @return mixed
     */
    public function allActive()
    {
        return $this->newQuery()->where('active', true)->get();
    }

    /**
     * Clears the current query instance.
     *
     * @return self
     */
    public function clearQuery()
    {
        $this->query = null;
        return $this;
    }

    /**
     * Fills the model with data from an array.
     *
     * @param array $data
     * @return self
     */
    public function fillFromArray(array $data)
    {
        foreach ($data as $attribute => $value){
            $this->getModel()->{$attribute} = $value;
        }
        return $this;
    }

    /**
     * Filter records by created date range.
     *
     * @param mixed $start_date
     * @param mixed $end_date
     * @return mixed
     */
    public function filter_by_created_at($start_date, $end_date = null)
    {
        $end_date= is_null($end_date)?$start_date:$end_date;
        return $this->query()->whereBetween('created_at', [$start_date, $end_date])->get();
    }

    /**
     * Filter records by a specific column and value.
     *
     * @param string $column
     * @param mixed $value
     * @return mixed
     */
    public function filter_by_column($column, $value)
    {
        return $this->query()->where($column, $value)->get();
    }

    /**
     * Find a record by its ID.
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->getNewModel()->find($id);
    }

    /**
     * Find a record and retrieve additional related data.
     *
     * @param int $id
     * @return mixed
     */
    public function findAndGet($id)
    {
        return $this->getNewModel()->findOrFail($id);
    }

    /**
     * Get the model instance associated with the repository.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Create a new query instance for the model.
     *
     * @return mixed
     */
    public function newQuery()
    {
        return $this->getModel()::query();
    }

    /**
     * Order records by a specific column.
     *
     * @param string $field
     * @param string $order
     * @return mixed
     */
    public function orderByColumn($field, $order= 'asc')
    {
        return $this->query()->orderBy($field, $order)->get();
    }

    /**
     * Paginate the query results.
     *
     * @param int $recordsByPage
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    public function paginate($recordsByPage = 20, $orderBy = 'created_at', $order = 'desc')
    {
        return $this->query()->orderBy($orderBy, $order)->paginate($recordsByPage);
    }

    /**
     * Pre-create logic before creating a new record.
     *
     * @throws Exception
     */
    public function preCreate()
    {
        $this->userIsPresent();
        try{
            $this->getModel()->uw_created = $this->user->id;
            $this->getModel()->save();
            return $this;
        }catch (Exception $e){
            report($e);
            throw new Exception('Exception in preCreate. '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Pre-delete logic before deleting a record.
     *
     * @throws Exception
     */
    public function preDelete()
    {
        $this->userIsPresent();
        try{
            $this->getModel()->delete();
            return $this;
        }catch (Exception $e){
            report($e);
            throw new Exception('Exception in preDelete. '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Pre-update logic before updating a record.
     *
     * @throws Exception
     */
    public function preUpdate()
    {
        $this->userIsPresent();
        try{
            $this->getModel()->uw_updated = $this->user->id;
            unset ($this->getModel()->repository);
            $this->getModel()->save();
            return $this;
        }catch (Exception $e){
            report($e);
            throw new Exception('Exception in preUpdate. '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Access the query instance, creating one if necessary.
     *
     * @return mixed
     */
    public function query()
    {
        if(is_null($this->query))
            $this->query = $this->newQuery();

        return $this->query;
    }

    /**
     * Set a new model for the repository.
     *
     * @return self
     */
    protected function setNewModel()
    {
        $this->model = $this->getNewModel();
        return $this;
    }

    /**
     * Set the model for the repository.
     *
     * @param mixed $model
     * @return self
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set the user associated with the repository.
     *
     * @param mixed $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Check if the user is present.
     *
     * @throws Exception
     */
    protected function userIsPresent()
    {
        if($this->user === null){
            $e= new Exception('User is not present.');
            report($e);
            throw $e;
        }
    }

    /**
     * Apply a where clause to the query.
     *
     * @param string $column
     * @param mixed $value
     * @return self
     */
    public function where($column, $value)
    {
        $this->model = $this->query()->where($column, $value);
        return $this;
    }

}
