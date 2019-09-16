<?php
$code = <<<CODE
<?php

namespace @{namespace};

use Closure;
use Illuminate\Database\Eloquent\Builder;
use @{toolPackage};
use @{commonModelPackage};
use App\Databases\Models\Exceptions\ModelInitializeFailException;
use App\Databases\Models\Exceptions\ModelNotFoundException;

abstract class @{class}
{

    /**
     * @var static|Builder
     */
    protected \$model;

    /**
     * CommonRepository constructor.
     * @throws ModelInitializeFailException
     */
    public function __construct()
    {
        try {
            \$this->_init();
        } catch (\Exception \$e) {
            throw new ModelInitializeFailException();
        }
    }

    /**
     * @throws \Exception
     */
    private function _init()
    {
        \$model = app()->make(static::model());
        if (!\$model instanceof @{commonModelName}) {
            throw new \Exception('"Class {\$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"');
        }
        \$this->model = \$model;
    }

    /**
     * @return string 可以实例化的模型类名
     */
    abstract protected function model();

    /**
     * @param Closure \$transaction
     * @param int     \$retry
     * @return mixed|string
     * @throws \Exception
     */
    public function transaction(Closure \$transaction, \$retry = 1)
    {
        return @{commonModelName}::transaction(\$transaction, \$retry);
    }

    /**
     * @param       \$id
     * @param array \$column
     * @return CommonModel
     * @throws ModelNotFoundException
     */
    public function find(\$id, array \$column = ['*'])
    {
        \$result = \$this->model->find(...func_get_args());
        if (is_null(\$result)) {
            throw new ModelNotFoundException();
        }
        return \$result;
    }

    /**
     * @param Closure \$when_function
     * @return mixed
     */
    public function query(Closure \$when_function)
    {
        return \$this->model->when(true, \$when_function);
    }
	
	/**
	 * Function: getListPlus
	 * Notes: Joker自定义分页加强版v2.0
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-27  16:11
	 * @param int \$pageSize
	 * @param int \$page
	 * @param array \$where
	 * @param array \$column
	 * @return array
	 */
	public function getListPlusTwo(int \$pageSize = 10, int \$page = 1,array \$where = [], array \$column = ['*'])
	{
		return \$this->model->where(\$where)->select(\$column)->jokerPaginate(\$pageSize, \$page);
	}
	
	/**
	 * Function: getListPlusTwo
	 * Notes: Joker自定义分页加强版v1.0
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-27  16:11
	 * @param int \$pageSize
	 * @param int \$page
	 * @param array \$where
	 * @param array \$column
	 * @return array
	 */
	public function getListPlus(int \$pageSize = 10, int \$page = 1,array \$where = [], array \$column = ['*'])
	{
		\$query = \$this->model->where(\$where)->select(\$column)->getQuery();
		return @{toolName}::basePaginate(\$query, \$pageSize, \$page);
	}
	
	/**
	 * Function: getList
	 * Notes: 分页
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  13:54
	 * @param int \$pageSize 每页需要多少
	 * @param int \$page 获取第几页
	 * @param array \$where
	 * @param array \$column
	 * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getList(int \$pageSize = 10, int \$page = 1,array \$where = [], array \$column = ['*'])
	{
		return \$this->model->where(\$where)->select(\$column)->offset((\$page-1) * \$pageSize)->limit(\$pageSize)->latest()->get();
	}
	
	/**
	 * Function: getCount
	 * Notes: 返回每列数量
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  14:12
	 * @param array \$where
	 * @param string \$column
	 * @return int
	 */
	public function getCount(array \$where = [], \$column = 'id', array \$whereIn = [])
	{
		if (count(\$whereIn) > 0) {
			return \$this->model->where(\$where)->whereIn(\$whereIn['column'], \$whereIn['value'])->count(\$column);
		}
		return \$this->model->where(\$where)->count(\$column);
	}
	
	public function first(array \$where, array \$column = ['*'])
	{
		return \$this->model->where(\$where)->select(\$column)->first();
	}
	
	public function firstWhereIn(string \$column, array \$whereInData, array \$columns = ['*'])
	{
		return \$this->model->whereIn(\$column, \$whereInData)->select(\$columns)->first();
	}
	
	/**
	 * Function: get
	 * Notes: 获取符合条件的数据
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  18:38
	 * @param array \$where
	 * @param array \$columns
	 * @param array \$whereIn
	 * @return int
	 */
	public function get(array \$where = [], array \$columns = ['*'], array \$whereIn = [])
	{
		if (count(\$whereIn) > 0) {
			return \$this->model->where(\$where)->whereIn(\$whereIn['column'], \$whereIn['value'])->select(\$columns)->latest()->get();
		}
		return \$this->model->where(\$where)->select(\$columns)->latest()->get();
	}
	
	
	/**
	 * Function: getPluck
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-16  11:24
	 * @param array \$column
	 * @param array \$where
	 * @return array
	 */
	public function getPluck(string \$column, array \$where = [])
	{
		return \$this->model->where(\$where)->pluck(\$column)->toArray();
	}
	
	/**
	 * Function: getPluck
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-16  11:24
	 * @param array \$column
	 * @param array \$where
	 * @return array
	 */
	public function getPluckWhereLike(string \$column, string \$likeColumn, string \$likeData)
	{
		return \$this->model->where(\$likeColumn, 'like', \$likeData)->pluck(\$column)->toArray();
	}
	
	/**
	 * Function: getColumnMin
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:42
	 * @param string \$column
	 * @param array \$where
	 * @param array \$whereIn
	 * @return mixed
	 */
	public function getColumnMin(string \$column, array \$where, array \$whereIn = [])
	{
		return \$this->model->where(\$where)
			->where(function (\$query) use (\$whereIn) {
				if (count(\$whereIn)) {
					\$query->whereIn(\$whereIn['column'], \$whereIn['value']);
				}
			})
			->min(\$column);
	}
}

CODE;

