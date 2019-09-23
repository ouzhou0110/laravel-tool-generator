<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands;

use Illuminate\Console\Command;

class ModelCRUDCommand extends Command
{
	use CommonTrait;
	
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
		$modelName = $this->argument('modelName');
		// 获取配置
		$config = self::getConfigInfo($modelName);
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
		// 加载模板
		require_once __DIR__ . '/Resources/CRUD/model.php';
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
		// 加载模板
		require_once __DIR__ . '/Resources/CRUD/action.php';
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
		// 加载模板
		require_once __DIR__ . '/Resources/CRUD/repository.php';
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
		// 加载模板
		require_once __DIR__ . '/Resources/CRUD/service.php';
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
		
		// 替换公共资源路径
		$code = str_replace('@{commonServicePackage}', self::SERVICE_COMMON, $code);
		$code = str_replace('@{commonService}', 'CommonService', $code);
		
		// 生成文件
		// 1. 拼接文件路径和文件名
		$file = $basePath . ($config->filePath ? $config->filePath . '/' : '') . $fileName . '.php';
		// 2. 生成文件
		self::save($file, $code);
	}
}
