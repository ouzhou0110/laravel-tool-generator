<?php

namespace OuZhou\LaravelToolGenerator;

use Illuminate\Support\ServiceProvider;
use OuZhou\LaravelToolGenerator\Console\Commands\ModelCommonClassGeneratorCommand;
use OuZhou\LaravelToolGenerator\Console\Commands\ModelCRUDCommand;

class GeneratorServiceProvider extends ServiceProvider
{
	/**
	 * 服务提供者加是否延迟加载.
	 *
	 * @var bool
	 */
	protected $defer = false; // 延迟加载服务
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				ModelCommonClassGeneratorCommand::class,
				ModelCRUDCommand::class,
			]);
		}
	}
	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
	
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
}
