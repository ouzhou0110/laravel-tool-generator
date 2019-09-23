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
		// 3. 先生成controller, 后生成路由，如果 controller 生成失败则放弃生成路由
		self::controllerGenerator($config, $model, $simple) && self::routeGenerator($config);
	}
	
	/**
	 * Function: routeGenerator
	 * Notes: 路由生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  14:55
	 * @param $config
	 */
	private static function routeGenerator($config)
	{
		// 检测是不是遵循那种规则：规则1=> Api/User/UserManage/IndexController 路由使用api.php
		// 规则2=> User/UserManage/IndexController 路由使用web.php
		// 通过前缀判断
		$config->firstPrefix !== 'api' ? WebRouter::generator($config) : ApiRouter::generator($config);
	}
	
	/**
	 * Function: controllerGenerator
	 * Notes: 控制器生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  15:30
	 * @param $config
	 * @param $model
	 * @param bool $simple
	 */
	private static function controllerGenerator($config, $model, $simple = false)
	{
		return $simple == false ? Controller::generator($config, $model) : SimpleController::generator($config, $model);
	}
}
