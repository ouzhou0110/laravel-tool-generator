<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands;

use Illuminate\Console\Command;
use OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller\Controller;
use OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller\SimpleController;
use OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route\ApiRouter;
use OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route\WebRouter;

class ControllerCommand extends Command
{
	use CommonTrait;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'ouzhou:controller {name} {--model=} {--simple=false}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '重写控制器生成方法，自定义开发';
	
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
		// 路径解析
		$config = self::getConfigInfo($this->argument('name'));
		// 模型名称--首字母大写
		$model = ucfirst(strtolower($this->option('model')));
		// controller是否简单
		$simple = strtolower($this->option('simple'));
//		echo $simple;

		$isWebInject = false; // web开头自动降级
		if ($config->firstPrefix === 'web') {
			// 方式2注入 == 避免大小写错误--首字母统统小写
			$config->filePath = lcfirst($config->filePath);
			// 去掉web
			$config->filePath = str_replace('web/', '', $config->filePath);
			unset($config->realPath[0]);
			--$config->level;
			$config->firstPrefix = lcfirst($config->realPath[1]);
			$isWebInject = true;
		} else if (strtolower($config->firstPrefix) !== 'api') {
		    $isWebInject = true;
        }
//			var_dump($config);
		
		// 判断是否存在
		$path = app_path('Http/Controllers/' . $config->filePath  . '/' . $config->fileName . '.php');
		if (file_exists($path)) {
			echo "Danger: 控制器已经存在 => $path";
			return false;
		}
		// 3. 先生成controller, 后生成路由，如果 controller 生成失败则放弃生成路由
		self::controllerGenerator($config, $model, $simple) && ( config('jokerLaravelGenerator.route') && self::routeGenerator($config, $isWebInject));
	}
	
	/**
	 * Function: routeGenerator
	 * Notes: 路由生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  14:55
	 * @param $config
	 * @param bool $isWeb
	 */
	private static function routeGenerator($config, bool $isWeb)
	{
		// 检测是不是遵循那种规则：规则1=> Api/User/UserManage/IndexController 路由使用api.php
		// 规则2=> User/UserManage/IndexController 路由使用web.php
		// 通过前缀判断
		$isWeb ? WebRouter::generator($config) : ApiRouter::generator($config);
	}
	
	/**
	 * Function: controllerGenerator
	 * Notes: 控制器生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  15:30
	 * @param $config
	 * @param $model
	 * @param string $simple
	 * @return bool
	 */
	private static function controllerGenerator($config, $model, $simple = 'false')
	{
		return $simple === 'false' ? Controller::generator($config, $model) : SimpleController::generator($config, $model);
	}
}
