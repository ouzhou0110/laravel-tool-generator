<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands;

use Illuminate\Console\Command;

class ModelCommonClassGeneratorCommand extends Command
{
	// 必须app开头
	const MODEL_COMMON_PATH = 'App\Databases\Models\Commons';
	const SERVICE_COMMON_PATH = 'App\Databases\Services\Commons';
	const REPOSITORY_COMMON_PATH = 'App\Databases\Repository\Commons';
	const ACTION_COMMON_PATH = 'App\Databases\Actions\Commons';
	
	// 异常路径
	const MODEL_EXCEPTION_PATH = 'App\Databases\Models\Exceptions';
	const SERVICE_EXCEPTION_PATH = 'App\Databases\Services\Exceptions';
	const REPOSITORY_EXCEPTION_PATH = 'App\Databases\Repository\Exceptions';
	const ACTION_EXCEPTION_PATH = 'App\Databases\Actions\Exceptions';
	
	const MODEL_NAME = 'CommonModel';
	const SERVICE_NAME = 'CommonService';
	const REPOSITORY_NAME = 'CommonRepository';
	const ACTION_NAME = 'CommonAction';
	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ouzhou:modelGenerator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动生成项目模型所需基础依赖类';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 注入model通用方法
		self::modelFunctions();
		// 注入model的通用异常处理
		self::modelExceptionsInit();
		
		// 注入action的通用方法
		self::actionFunctions();
		// 注入action的通用异常处理
		self::actionExceptionsInit();
		
		// 注入repository的通用方法
		self::repositoryFunctions();
		// 注入repository的通用异常处理
		self::repositoryExceptionsInit();
    }
	
	
	/**
	 * Function: modelFunctions
	 * Notes: 通用模型配置
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:05
	 */
    private static function modelFunctions()
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*
*/
namespace @{namespace};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


abstract class @{name} extends Model
{
    use SoftDeletes;
    public    \$timestamps = true; // 开启时间维护
    protected \$primaryKey = 'id'; // 默认主键
    protected \$guarded    = ['id']; // 禁止填充
    protected \$dateFormat = 'Y-m-d H:i:s';// 时间格式
    protected \$dates      = ['deleted_at', 'created_at', 'updated_at']; // 时间转换
	protected \$hidden = ['password'];

    /**
     * 重写模型insert方法 自动注入时间,弥补了Laravel Model::insert() 不自动加入时间的问题。
     * @param array \$data
     * @return bool
     */
    public function insert(array \$data)
    {
        if (count(\$data, true) === count(\$data)) {
            foreach (\$data as &\$item) {
                \$item = self::mergeDataAt(\$item);
            }
            unset(\$item);
        } else {
            \$data = self::mergeDataAt(\$data);
        }
        return static::insert(\$data);
    }

    /**
     * @param array \$data
     * 重写 Model::insertGetId() 方法 添加时间
     * @param null  \$args
     * @return int|mixed
     */
    public function insertGetId(array \$data, \$args = null)
    {
        \$data = self::mergeDataAt(\$data);
        return static::insertGetId(\$data, \$args);
    }

    public static function mergeDataAt(array \$data)
    {

        \$time = now()->toDateString();
        \$data = array_merge(\$data, [
            'created_at' => \$time,
        ]);
        return \$data;
    }
}

CODE;
		
		// 替换数据--name
		$code = str_replace('@{name}', self::MODEL_NAME, $code);
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::MODEL_COMMON_PATH, $code);
		// 存储路径
		$file = './' . lcfirst(str_replace('\\', '/', self::MODEL_COMMON_PATH)) . '/' . self::MODEL_NAME . '.php';
		
		self::save($file, $code);

	}
	
	
	/**
	 * Function: actionFunctions
	 * Notes: 通用模型CUD操作生成
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:38
	 */
	private static function actionFunctions()
	{
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
	 * Notes:
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
}
CODE;
		
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::ACTION_COMMON_PATH, $code);
		// 替换数据--class
		$code = str_replace('@{class}', self::ACTION_NAME, $code);
		// 替换数据--commonModelPackage
		$code = str_replace('@{commonModelPackage}', self::MODEL_COMMON_PATH . '\\' . self::MODEL_NAME, $code);
		// 替换数据--commonModelName
		$code = str_replace('@{commonModelName}', self::MODEL_NAME, $code);
		
		// 存储路径
		$file = './' . lcfirst(str_replace('\\', '/', self::ACTION_COMMON_PATH)) . '/' . self::ACTION_NAME . '.php';
		
		self::save($file, $code);
		
	}
	
	/**
	 * Function: repositoryFunctions
	 * Notes: 模型通用R操作生成
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:46
	 */
	private static function repositoryFunctions()
	{
		$code = <<<CODE
<?php

namespace @{namespace};

use Closure;
use App\Tools\Library\JokerTool;
use Illuminate\Database\Eloquent\Builder;
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
		return JokerTool::basePaginate(\$query, \$pageSize, \$page);
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
		
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::REPOSITORY_COMMON_PATH, $code);
		// 替换数据--class
		$code = str_replace('@{class}', self::REPOSITORY_NAME, $code);
		// 替换数据--commonModelPackage
		$code = str_replace('@{commonModelPackage}', self::MODEL_COMMON_PATH . '\\' . self::MODEL_NAME, $code);
		// 替换数据--commonModelName
		$code = str_replace('@{commonModelName}', self::MODEL_NAME, $code);
		
		// 存储路径
		$file = './' . lcfirst(str_replace('\\', '/', self::REPOSITORY_COMMON_PATH)) . '/' . self::REPOSITORY_NAME . '.php';
		
		self::save($file, $code);
	}
	
	/**
	 * Function: modelExceptions
	 * Notes: 模型异常初始化
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:25
	 */
	private static function modelExceptionsInit()
	{
		$exceptions = [
			['class' => 'ModelException', 'message' => '模型错误'],
			['class' => 'ModelInitializeFailException', 'message' => '初始化模型失败'],
			['class' => 'ModelNotFoundException', 'message' => '模型不存在'],
		];
		
		// 执行生成程序
		self::exceptionGenerator($exceptions, self::MODEL_EXCEPTION_PATH);
		
	}
	
	/**
	 * Function: modelExceptions
	 * Notes: 模型异常初始化
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:25
	 */
	private static function actionExceptionsInit()
	{
		$exceptions = [
			['class' => 'ActionException', 'message' => '模型方法异常'],
		];
		
		// 执行生成程序
		self::exceptionGenerator($exceptions, self::ACTION_EXCEPTION_PATH);
		
	}
	
	/**
	 * Function: modelExceptions
	 * Notes: 模型异常初始化
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:25
	 */
	private static function repositoryExceptionsInit()
	{
		$exceptions = [
			['class' => 'ClassifyIdException', 'message' => '分类选择错误'],
		];
		
		// 执行生成程序
		self::exceptionGenerator($exceptions, self::REPOSITORY_EXCEPTION_PATH);
		
	}
	
	/**
	 * Function: exceptionFormat
	 * Notes: 异常格式
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:11
	 * @return string
	 */
	private static function exceptionFormat(): string
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*
*/
namespace @{namespace};

use Throwable;

class @{class} extends \Exception
{
    public function __construct(string \$message = "", int \$code = 0, Throwable \$previous = null)
    {
        empty(\$message) && \$message = '@{message}: ' . \$message;
        parent::__construct(\$message, \$code, \$previous);
    }
}

CODE;
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::MODEL_EXCEPTION_PATH, $code);
		return $code;
	}
	
	/**
	 * Function: exceptionGenerator
	 * Notes: 异常生成器
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  18:23
	 * @param array $exceptions
	 */
	private static function exceptionGenerator(array $exceptions, string $exceptionKind)
	{
		foreach ($exceptions as $v) {
			// 第一个异常
			$code = self::exceptionFormat();
			// 替换数据--name
			$code = str_replace('@{class}', $v['class'], $code);
			// 替换数据--message
			$code = str_replace('@{message}', $v['message'], $code);
			// 存储路径
			$file = './' . lcfirst(str_replace('\\', '/', $exceptionKind)) . '/' . $v['class'] . '.php';
			self::save($file, $code);
		}
	}
	
	
	/**
	 * Function: saveToFile
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  15:34
	 * @param $file
	 * @param $code
	 * @return string
	 */
	private static function save($file, $code)
	{
		$dir = dirname($file);
		if (file_exists($file)) {
			echo 'Exists => Path:' . $file . PHP_EOL;
			return false;
		}
		if (!file_exists($dir)) {
			mkdir($dir, 777, true);
		}
		$result = file_put_contents($file, $code);
		if ($result ) {
			echo 'Success => Path:' . $file . PHP_EOL;
			return true;
		}
		echo 'Fail => Path:' . $file . PHP_EOL;
		return false;
	}
	
}
