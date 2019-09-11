<?php

namespace Ouzhou\LaravelToolGenerator\Console\Commands;

use Illuminate\Console\Command;

class OuzhouModelCRUDCommand extends Command
{
	const MODEL_DIR = './app/Databases/Models/';
	const SERVICE_DIR = './app/Databases/Services/';
	const ACTION_DIR = './app/Databases/Actions/';
	const REPOSITORY_DIR = './app/Databases/Repository/';
	
	const MODEL_NAMESPACE = 'App\Databases\Models';
	const SERVICE_NAMESPACE = 'App\Databases\Services';
	const REPOSITORY_NAMESPACE = 'App\Databases\Repository';
	const ACTION_NAMESPACE = 'App\Databases\Actions';
	
	const MODEL_COMMON = 'App\Databases\Models\Commons\CommonModel';
	const SERVICE_COMMON = 'App\Databases\Services\Commons\CommonService';
	const REPOSITORY_COMMON = 'App\Databases\Repository\Commons\CommonRepository';
	const ACTION_COMMON = 'App\Databases\Actions\Commons\CommonAction';
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'ouzhou:model {modelName}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '根据模型名称自动生成Model、Action、Repository、Service类（数据库表的名称，驼峰式命名）';
	
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
		// 获取配置
		$config = self::getConfigInfo($this);
		// 生成model原形
		self::modelGenerator($config);
		// 生成action原形
		self::actionGenerator($config);
		// 生成repository原形
		self::repositoryGenerator($config);
		// 生成service原形
		self::serviceGenerator($config);
	}
	
	
	/**
	 * Function: modelGenerator
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  15:41
	 * @param $config
	 */
	private static function modelGenerator($config)
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

use @{modelCommon};
/**
 * Class @{fileName}
 */
class @{fileName} extends @{commonModel}
{
	// 数据表名
	protected \$table = '@{tableName}';
	
	/**
	* 远程一对多或者远程一对一的参数提示
	* @param1 最终目标表
	* @param2 中间表
	* @param3 中间表外键 -- 对应当前 model 主键
	* @param4 最终目标表外键 -- 对应中间表主键
	* @param5 当前模型主键
	* @param6 中间表主键
	*/
	

}
CODE;
		// 拼接对应数据
		// namespace
		$namespace = self::MODEL_NAMESPACE . $config->namespace;
		// fileName
		$fileName = $config->fileName . 'Model';
		// 通用模型名称
		$commonModel = 'CommonModel';
		// 通用资源路径
		$modelCommon = self::MODEL_COMMON;
		// 储存路径
		$basePath = self::MODEL_DIR;
		
		// 替换目标数据
		$code = str_replace('@{namespace}', $namespace, $code); // 命名空间
		$code = str_replace('@{modelCommon}', $modelCommon, $code); // 通用资源
		$code = str_replace('@{fileName}', $fileName, $code); // 类名
		$code = str_replace('@{commonModel}', $commonModel, $code); // 通用资源名称
		$code = str_replace('@{tableName}', $config->tableName, $code); // 通用资源名称
		
		// 生成文件
		// 1. 拼接文件路径和文件名
		$file = $basePath . ($config->filePath ? $config->filePath . '/' : '') . $fileName . '.php';
		// 2. 生成文件
		self::save($file, $code);
	}
	
	/**
	 * Function: actionGenerator
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  16:32
	 * @param $config
	 */
	private static function actionGenerator($config)
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

use @{modelPackage};
use Illuminate\Database\Eloquent\Builder;
use @{modelCommon};

/**
 * 用于数据库的各种 CUD(写、改、删) 操作
 * Class @{fileName}
 */
class @{fileName} extends @{commonModel}
{

    /**
     * @var @{modelName}|Builder
     * 模型
     */
    protected \$model;

    /**
     * @return string 可以实例化的模型类名
     */
    protected function model(): string
    {
        return @{modelName}::class;
    }
    
}
CODE;
		
		// 拼接对应数据
		// namespace
		$namespace = self::ACTION_NAMESPACE . $config->namespace;
		// fileName
		$fileName = $config->fileName . 'Action';
		// 通用模型名称
		$commonModel = 'CommonAction';
		// 通用资源路径
		$modelCommon = self::ACTION_COMMON;
		// 储存路径
		$basePath = self::ACTION_DIR;
		// 模型名称
		$modelName = $config->fileName . 'Model';
		// 模型的包路径
		$modelPackage = self::MODEL_NAMESPACE . $config->namespace . '\\' . $modelName;
		
		// 替换目标数据
		$code = str_replace('@{namespace}', $namespace, $code); // 命名空间
		$code = str_replace('@{modelCommon}', $modelCommon, $code); // 通用资源
		$code = str_replace('@{fileName}', $fileName, $code); // 类名
		$code = str_replace('@{commonModel}', $commonModel, $code); // 通用资源名称
		$code = str_replace('@{tableName}', $config->tableName, $code); // 通用资源名称
		
		$code = str_replace('@{modelPackage}', $modelPackage, $code); // 模型包路径
		$code = str_replace('@{modelName}', $modelName, $code); // 模型名称
		
		// 生成文件
		// 1. 拼接文件路径和文件名
		$file = $basePath . ($config->filePath ? $config->filePath . '/' : '') . $fileName . '.php';
		// 2. 生成文件
		self::save($file, $code);
	}
	
	/**
	 * Function: repositoryGenerator
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  16:32
	 * @param $config
	 */
	private static function repositoryGenerator($config)
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

use @{modelPackage};
use Illuminate\Database\Eloquent\Builder;
use @{modelCommon};

/**
 * 用于数据库的各种 R 操作
 * Class @{fileName}
 */
class @{fileName} extends @{commonModel}
{

    /**
     * @var @{modelName}|Builder
     * 模型
     */
    protected \$model;

    /**
     * @return string 可以实例化的模型类名
     */
    protected function model(): string
    {
        return @{modelName}::class;
    }
    
}
CODE;
		
		// 拼接对应数据
		// namespace
		$namespace = self::REPOSITORY_NAMESPACE . $config->namespace;
		// fileName
		$fileName = $config->fileName . 'Repository';
		// 通用模型名称
		$commonModel = 'CommonRepository';
		// 通用资源路径
		$modelCommon = self::REPOSITORY_COMMON;
		// 储存路径
		$basePath = self::REPOSITORY_DIR;
		// 模型名称
		$modelName = $config->fileName . 'Model';
		// 模型的包路径
		$modelPackage = self::MODEL_NAMESPACE . $config->namespace . '\\' . $modelName;
		
		// 替换目标数据
		$code = str_replace('@{namespace}', $namespace, $code); // 命名空间
		$code = str_replace('@{modelCommon}', $modelCommon, $code); // 通用资源
		$code = str_replace('@{fileName}', $fileName, $code); // 类名
		$code = str_replace('@{commonModel}', $commonModel, $code); // 通用资源名称
		$code = str_replace('@{tableName}', $config->tableName, $code); // 通用资源名称
		
		$code = str_replace('@{modelPackage}', $modelPackage, $code); // 模型包路径
		$code = str_replace('@{modelName}', $modelName, $code); // 模型名称
		
		// 生成文件
		// 1. 拼接文件路径和文件名
		$file = $basePath . ($config->filePath ? $config->filePath . '/' : '') . $fileName . '.php';
		// 2. 生成文件
		self::save($file, $code);
	}
	
	/**
	 * Function: serviceGenerator
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  16:32
	 * @param $config
	 */
	private static function serviceGenerator($config)
	{
		$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

// 引入当前模型的CRUD操作
use @{actionPackage};
use @{repositoryPackage};

/**
 * service 层，处理业务逻辑
 * Class @{fileName}
 */
class @{fileName}
{

	// CUD 操作
	private \$@{action};
	// R操作
	private \$@{repository};
	
	// 注入CRUD操作
	public function __construct()
	{
		\$this->@{action} = app()->make(@{actionClass}::class);
		\$this->@{repository} = app()->make(@{repositoryClass}::class);
	}
    
}
CODE;
		
		// 拼接对应数据
		// namespace
		$namespace = self::SERVICE_NAMESPACE . $config->namespace;
		// fileName
		$fileName = $config->fileName . 'Service';
		// 储存路径
		$basePath = self::SERVICE_DIR;
		
		// action 类名
		$auctionClass = $config->fileName . 'Action';
		// action 变量名
		$auctionName = lcfirst($auctionClass);
		// action 包路径
		$actionPackage = self::ACTION_NAMESPACE . $config->namespace . '\\' . $auctionClass;
		
		// repository 类名
		$repositoryClass = $config->fileName . 'Repository';
		// repository 变量名
		$repositoryName = lcfirst($repositoryClass);
		// repository 包路径
		$repositoryPackage = self::REPOSITORY_NAMESPACE . $config->namespace . '\\' . $repositoryClass;
		
		
		// 替换目标数据
		$code = str_replace('@{namespace}', $namespace, $code); // 命名空间
		$code = str_replace('@{fileName}', $fileName, $code); // 类名
		
		// CUD 操作类相关数据
		$code = str_replace('@{actionPackage}', $actionPackage, $code); // 模型包路径
		$code = str_replace('@{action}', $auctionName, $code); // 模型名称
		$code = str_replace('@{actionClass}', $auctionClass, $code); // 类名
		
		// R 操作类相关数据
		$code = str_replace('@{repositoryPackage}', $repositoryPackage, $code); // 模型包路径
		$code = str_replace('@{repository}', $repositoryName, $code); // 模型名称
		$code = str_replace('@{repositoryClass}', $repositoryClass, $code); // 类名
		
		// 生成文件
		// 1. 拼接文件路径和文件名
		$file = $basePath . ($config->filePath ? $config->filePath . '/' : '') . $fileName . '.php';
		// 2. 生成文件
		self::save($file, $code);
	}
	
	
	/**
	 * Function: getConfigInfo
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  15:19
	 * @param $_this
	 * @return Object
	 */
	private static function getConfigInfo($_this)
	{
		// 获取文件路径以及文件名
		$baseName = $_this->argument('modelName');
		
		// 信息数组
		$arr = preg_split('/["\\/","\\\"]/', $baseName);
		
		// 文件名
		$fileName = end($arr);
		// 文件路径
		if (count($arr) > 1) { // 如果指定文件路径就删除文件名
			unset($arr[array_key_last($arr)]);
			$filePath = implode('/', $arr);
			$namespace = '\\' . implode('\\', $arr);
		} else {
			$filePath = ''; // 使用默认路径
			$namespace = '';
		}
		
		// 模型名称--驼峰转化为下滑线
		// 根据大写替换成特殊字符,并保留大写字母
		$tableName = preg_replace('/([A-Z])/', '_\\1', $fileName);
		// 去除第一个特殊字符
		$tableName = substr($tableName, 1);
		// 转化为小写
		$tableName = strtolower($tableName);
		
		
		return (object)[
			'filePath' => $filePath, // 文件路径
			'fileName' => $fileName, // 文件名
			'tableName' => $tableName,
			'namespace' => $namespace
		];
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
