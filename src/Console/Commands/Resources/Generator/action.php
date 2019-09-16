<?php
$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*
*/
namespace @{namespace};

use Closure;
use Illuminate\Database\Eloquent\Builder;
use @{commonModelPackage};
use App\Databases\Models\Exceptions\ModelInitializeFailException;
use Illuminate\Database\Eloquent\Model as ModelAlias;

abstract class @{class}
{
	
	/**
	 * @var static|Builder
	 */
	protected \$model;
	
	/**
	 * CommonAction constructor.
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
	private	function _init(): void
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
	abstract protected function model(): string;
	
	/**
	 * @param Closure \$transaction
	 * @param int \$retry
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function transaction(Closure \$transaction, \$retry = 1)
	{
		return @{commonModelName}::transaction(\$transaction, \$retry);
	}
	
	/**
	 * @param array \$data
	 * @return Builder|ModelAlias
	 */
	public function create(array \$data)
	{
		return \$this->model->create(\$data);
	}
	
	/**
	 * @param array \$where
	 * @param array \$data
	 * @return int
	 */
	public function update(array \$where, array \$data)
	{
		return \$this->model->where(\$where)->update(\$data);
	}
	
	/**
	 * Function: updateWhereIn
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-16  11:52
	 * @param array \$where
	 * @param array \$whereIn
	 * @param array \$data
	 * @return int
	 */
	public function updateWhereIn(array \$whereIn, array \$data, array \$where = [])
	{
		return \$this->model->where(\$where)->whereIn(\$whereIn['column'], \$whereIn['value'])->update(\$data);
	}
	
	/**
	 * @param array \$where
	 * @return mixed
	 */
	public function destroy(array \$where)
	{
		return \$this->model->where(\$where)->delete();
	}
	
	/**
	 * @param string \$column
	 * @return mixed
	 */
	public function destroyByNullColumn(string \$column = 'deleted_at')
	{
		return \$this->model->whereNull(\$column)->delete();
	}
	
	public function destroyWhereIn(string \$column, array \$whereInData)
	{
		return \$this->model->whereIn(\$column, \$whereInData)->delete();
	}
	
	/**
	 * Function: updateBatch
	 * Notes: 批量更新
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-30  12:02
	 * @param array \$needUpdatedData
	 * @param string \$referenceColumn
	 * @return bool
	 * @throws \Exception
	 */
	public function updateBatch(array \$needUpdatedData = [], string \$referenceColumn = 'id')
	{
		try {
			// \$referenceColumn：筛选条件
			if (!count(\$needUpdatedData)) {
				throw new \Exception('数据为空！');
			}
			
			// 获取表名 -- 当前环境
			\$tableName = \$this->model->getTable();
			// 获取需要更新的字段-排除参考字段
			\$needUpdatedColumns = array_diff(array_keys(array_first(\$needUpdatedData)), [\$referenceColumn]);
			
			/*****方便抽离出来写成单个方法--此时只是为了做一个统配*****/
			// 解决sql语句过长，执行速度反而较慢问题
			\$divideLen = 200;
			// 获取可以分成几组
			\$num = ceil(count(\$needUpdatedData) / \$divideLen);
			// 返回值
			\$data = [];
			// 起始值
			\$start = 0;
			// 切割
			while (\$start < \$num) {
				\$data[] = array_slice(\$needUpdatedData, \$start * \$divideLen, \$divideLen);
				\$start++;
			}
			
			/*****方便抽离出来写成单个方法*****/
			// 循环执行更新
			foreach (\$data as \$needUpdatedData1) {
				// \$q: sql语句
				\$q = 'UPDATE ' . \$tableName . ' SET ';
				// 遍历需要更新的字段
				foreach (\$needUpdatedColumns as \$uColumn) {
					\$q .= \$uColumn . ' = CASE ';
					// 拼接当前字段所需值
					foreach (\$needUpdatedData1 as \$data) {
						// \$data[\$referenceColumn]：搜寻条件的值，\$data[\$uColumn]：需要更新字段的值
						\$q .= 'WHEN ' . \$referenceColumn . ' = ' . \$data[\$referenceColumn] . " THEN '" . \$data[\$uColumn] . "' ";
					}
					// 拼接结束符-否则等于自身，放弃修改
					\$q .= 'ELSE ' . \$uColumn . ' END, ';
				}
				
				// 筛选条件的值
				\$whereIn = '';
				// 拼接筛选条件的值
				foreach (\$needUpdatedData1 as \$data) {
					\$whereIn .= "'\$data[\$referenceColumn]',";
				}
				// 去除尾逗号，并将筛选条件拼接完整
				\$q = rtrim(\$q, ', ') . ' WHERE ' . \$referenceColumn . ' IN (' . rtrim(\$whereIn, ', ') . ')';
				// 执行更新
				if (!\DB::update(\DB::raw(\$q))) {
					throw new \Exception('更新失败！');
				}
			}
			// 执行成功
			return true;
		} catch (\Exception \$e) {
			throw \$e;
		}
	}
	
	/**
     * Function: batchInsert
     * Notes: 批量插入
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-14  上午11:05
     * @param array \$data
     * @param string \$addTimeFieldName
     * @return mixed
     */
    public function batchInsert(array \$data, string \$addTimeFieldName = 'created_at')
    {
        if (count(\$data, true) === count(\$data)) {
            foreach (\$data as &\$item) {
                \$item = self::mergeDataAt(\$item, \$addTimeFieldName);
            }
            unset(\$item);
        } else {
            \$data = self::mergeDataAt(\$data,\$addTimeFieldName);
        }
        \$tableName = \$this->model->getTable();
        return \DB::table(\$tableName)->insert(\$data);
    }

    private function mergeDataAt(array \$data, \$addTimeFieldName)
    {
        return array_merge(\$data, [
            \$addTimeFieldName => now()->toDateString(),
        ]);
    }
}
CODE;


