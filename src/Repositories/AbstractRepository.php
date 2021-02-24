<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

//use Doctrine\Common\Collections\Collection;
//use MyBlog\User;

//use Illuminate\Support\Collection;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Container\Container as App;
use Hyperf\Utils\Collection;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Baseapp\Contracts\RepositoryInterface;
use Swoolecan\Baseapp\Contracts\CriteriaInterface;
use Swoolecan\Baseapp\Criteria\Criteria;
//use Bosnadev\Repositories\Exceptions\RepositoryException;

abstract class AbstractRepository implements RepositoryInterface, CriteriaInterface
{
    use TraitField;
    use TraitData;
    use TraitTree;
    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @Inject                
     * @var ResourceContainer
     */
    public $resource;

    /** 
     * @var AbstractModel
     */
    protected $model;

    /**
     * @var App
     */
    private $app;

    protected $newModel;
    protected $pointModel;

    /**
     * @Inject                
     * @var Collection
     */
    protected $criteria;

    /**
     * @var query
     */
    protected $query;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * Prevents from overwriting same criteria in chain usage
     * @var bool
     */
    protected $preventCriteriaOverwriting = false;

    public function __call($name, $arguments)
    {   
        return $this->model->{$name}(...$arguments);
    }

    /**
     * @param $resource
     */
    public function init($params = null)//$resource)//App $app, Collection $collection)
    {
        $modelCode = !empty($this->pointModel) ? $this->pointModel : get_called_class();
        $this->model = $this->resource->getObject('model', $modelCode);
        //$this->criteria = $collection;
        $this->resetScope();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getModelObj($code, $params = [])
    {
        return $this->resource->getObject('model', $code);
    }

    public function getCollectionClass($code = null)
    {
        return $this->resource->getClassName('collection', $code ? $code : get_called_class());
    }

    public function getResourceClass($code = null)
    {
        return $this->resource->getClassName('resource', $code ? $code : get_called_class());
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = null)
    {
        $columns = $columns ?? $this->_getColumns();

        $query = $this->applyCriteria();
        return $query->limit(2000)->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 25, $columns = null)
    {
        $columns = $columns ?? $this->_getColumns();
        $query = $this->applyCriteria();
        return $this->query->paginate($perPage, $columns);
    }

    protected function _getColumns()
    {
        return ['*'];
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * @param  string $value
     * @param  string $key
     * @return array
     */
    /*public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);
        if (is_array($lists)) {
            return $lists;
        }
        return $lists->all();
    }*/

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * save a model without massive assignment
     *
     * @param array $data
     * @return bool
     */
    public function saveModel(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }
        return $this->model->save();
    }

    /**
     * @param $info
     * @param array $data
     * @return mixed
     */
    public function updateInfo($info, array $data)
    {
        return $info->update($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param  array $data
     * @param  $id
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteInfo($info, $number)
    {
        $canDelete = $info->canDelete();
        if (empty($canDelete)) {
            $message = $number ? "已删除 {$number} 条信息" : '';
            $message .= $info[$info->getNameField()] . '信息无法被删除';
            return $this->resource->throwException(403, $message);
        }
        return $info->delete();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param array $where
     * @param array $columns
     * @param bool $or
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();

        $model = $this->model;

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        return $model->get($columns);
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    /*public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }*/

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });

            // Remove old criteria
            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * @return $this
     */
    public function applyCriteria($query = null)
    {
        if ($this->skipCriteria === true) {
            return $this;
        }
        $query = is_null($query) ? $this->getQuery() : $query;

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $criteria->apply($query, $this);
            }
        }

        return $query;
    }

    protected function getQuery()
    {
        //if (empty($this->query)) {
            $this->query = $this->model->query();
        //}
        return $this->query;
    }
}
