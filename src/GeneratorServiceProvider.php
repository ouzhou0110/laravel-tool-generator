<?php

namespace OuZhou\LaravelToolGenerator;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use OuZhou\LaravelToolGenerator\Console\Commands\ModelCommonClassGeneratorCommand;
use OuZhou\LaravelToolGenerator\Console\Commands\ModelCRUDCommand;
use OuZhou\LaravelToolGenerator\Tools\StaticClasses\JokerPaginator;

class GeneratorServiceProvider extends ServiceProvider
{
	/**
	 * 服务提供者加是否延迟加载.
	 *
	 * @var bool
	 */
	protected $defer = false; // 延迟加载服务
	
	/**
	 * z注入标识
	 */
	const JOKER_INJECT_TOKEN = 'e10adc3949ba59abbe56e057f20f883e';
	
	/**
	 * 跨域注册--获取注册位置标识
	 */
	const JOKER_MIDDLEWARE_KERNEL = 'protected $middleware = [';
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		/******************添加生成model的常用命令****************/
		if ($this->app->runningInConsole()) {
			$this->commands([
				ModelCommonClassGeneratorCommand::class,
				ModelCRUDCommand::class,
			]);
		}
		/*******************直接生成model通用配置*******************/
		Artisan::call('ouzhou:modelGenerator');
		
		/**********************发布跨域配置****************************/
		$this->publishes([
			__DIR__ . '\Tools\Configs\jokerEnableCrossRequest.php' => config_path('jokerEnableCrossRequest.php'),
		], 'config');
		Artisan::call('vendor:publish', [
			'--tag' => 'config'
		]);

		/***********为跨域注入到Kernel.php的middleware数组中**************/
		$data = file_get_contents(app_path('Http/Kernel.php'));
		if (false === strpos($data, self::JOKER_INJECT_TOKEN)) { // 是否已经注入，避免重复注入
			if (false !== strpos($data, self::JOKER_MIDDLEWARE_KERNEL)) { // 检查是否版本更新，导致定位符不存在了
				self::jokerEnableCrossRequestInjectKernel($data);
			}
		}
		
		/********************判断.env配置文件是否存在--不存在就复制一份**********/
		file_exists(base_path('.env')) || copy(base_path('.env.example'), base_path('.env'));
		
		/********************判断.env文件中是不是已经存在校验码**********/
		$data = file_get_contents('.env');
		/************为自定义登录验证注入配置到.env**************/
		if (false === strpos($data, self::JOKER_INJECT_TOKEN)) {
			self::jokerAuthInjectEnv();
		}
	}
	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		JokerPaginator::injectIntoBuilder();
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		// 因为延迟加载 所以要定义 provides 函数 具体参考laravel 文档
		return [''];
	}
	
	/**
	 * Function: jokerAuthInjectEnv
	 * Notes: 将自定义的登录验证配置注入当前框架
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:39
	 */
	private static function jokerAuthInjectEnv()
	{
		// 向文件加入JokerAuth的配置
		$code = <<< CODE
		
#校验码-@{token}
#登录方式--cookie
LOGIN_METHOD=cookie
#登录方式--token-header
#LOGIN_METHOD=token
CODE;
		// 替换唯一标识符
		$code = str_replace('@{token}', self::JOKER_INJECT_TOKEN, $code);
		// 执行追加
		file_put_contents(base_path('.env'), $code, FILE_APPEND);
		// 发布到通用配置里面
		file_put_contents(base_path('.env.example'), $code, FILE_APPEND);
	}
	
	private static function jokerEnableCrossRequestInjectKernel($data)
	{
		$code = <<< CODE
	// 注入标识：@{token}
	protected \$middleware = [
		\OuZhou\LaravelToolGenerator\Tools\Middlewares\EnableCrossRequestMiddleware::class,
CODE;
		// 替换唯一标识符
		$code = str_replace('@{token}', self::JOKER_INJECT_TOKEN, $code);
		$data = str_replace(self::JOKER_MIDDLEWARE_KERNEL, $code, $data);
		// 重新写入文件
		file_put_contents(app_path('Http/Kernel.php'), $data);
	}
}
