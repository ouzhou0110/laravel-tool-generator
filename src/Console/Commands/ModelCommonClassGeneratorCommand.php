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
	
	// 模型名称
	const MODEL_NAME = 'CommonModel';
	const SERVICE_NAME = 'CommonService';
	const REPOSITORY_NAME = 'CommonRepository';
	const ACTION_NAME = 'CommonAction';
	
	// 工具名称
	const TOOL_PATH = 'OuZhou\LaravelToolGenerator\Tools\StaticClasses\JokerTool';
	const TOOL_NAME = 'JokerTool';
	
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
		
		// 注入service的通用方法
		self::serviceFunctions();
		// 注入service的通用异常处理
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
		// 加载模型
		require_once __DIR__ . '/Resources/Generator/model.php';
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
		// 加载模型
		require_once __DIR__ . '/Resources/Generator/action.php';
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
		// 加载模型
		require_once __DIR__ . '/Resources/Generator/repository.php';
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::REPOSITORY_COMMON_PATH, $code);
		// 替换数据--class
		$code = str_replace('@{class}', self::REPOSITORY_NAME, $code);
		// 替换数据--commonModelPackage
		$code = str_replace('@{commonModelPackage}', self::MODEL_COMMON_PATH . '\\' . self::MODEL_NAME, $code);
		// 替换数据--commonModelName
		$code = str_replace('@{commonModelName}', self::MODEL_NAME, $code);
		// 替换数据--toolPackage
		$code = str_replace('@{toolPackage}', self::TOOL_PATH, $code);
		// 替换数据--toolPackage
		$code = str_replace('@{toolName}', self::TOOL_NAME, $code);
		
		// 存储路径
		$file = './' . lcfirst(str_replace('\\', '/', self::REPOSITORY_COMMON_PATH)) . '/' . self::REPOSITORY_NAME . '.php';
		
		self::save($file, $code);
	}
	
	/**
	 * Function: serviceFunctions
	 * Notes: service的操作，自动注入方法
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-16  16:28
	 */
	private static function serviceFunctions()
	{
		// 加载模型
		require_once __DIR__ . '/Resources/Generator/service.php';
		// 替换数据--namespace
		$code = str_replace('@{namespace}', self::SERVICE_COMMON_PATH, $code);
		// 替换数据--class
		$code = str_replace('@{class}', self::SERVICE_NAME, $code);
		// 替换数据--trait
		$code = str_replace('@{trait}', 'JokerApiResponseInjector', $code);
		// 替换数据--traitNamespace
		$code = str_replace('@{traitNamespace}', 'OuZhou\LaravelToolGenerator\Tools\Traits\JokerApiResponseInjector', $code);
		
		// 存储路径
		$file = './' . lcfirst(str_replace('\\', '/', self::SERVICE_COMMON_PATH)) . '/' . self::SERVICE_NAME . '.php';
		
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
		// 加载模型--多次调用，需要引用多次
		require __DIR__ . '/Resources/Generator/exception.php';
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
			echo 'Exists => Path: "' . $file . '"' . PHP_EOL;
			return false;
		}
		if (!file_exists($dir)) {
			if (!mkdir($dir, 777, true) && !is_dir($dir)) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
			}
		}
		$result = file_put_contents($file, $code);
		if ($result) {
			echo 'Success => Path: "' . $file . '"' . PHP_EOL;
			return true;
		}
		echo 'Fail => Path: "' . $file . '"' . PHP_EOL;
		return false;
	}
	
}
