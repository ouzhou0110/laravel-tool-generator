<?php
namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller;

use OuZhou\LaravelToolGenerator\Console\Commands\CommonTrait;

class Controller extends Templates
{
	use CommonTrait;
	
	/**
	 * Function: generator
	 * Notes: 生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  15:29
	 * @param $config
	 * @param $model
	 * @return string
	 */
	public static function generator($config, $model)
	{
		$data = self::controllerMode($config, $model);
		
		$path = app_path('Http/Controllers/' . $config->filePath  . '/' . $config->fileName . '.php');
		return self::save($path, $data);
	}
	
	/**
	 * Function: simpleControllerMode
	 * Notes: 简单控制器模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  9:29
	 */
	private static function controllerMode($config, $model):string
	{
		// 获取模型
		$data = self::common();
		// 初始化配置
		$index = self::indexConfig(self::index(), $config, $model);
		$create = self::createConfig(self::create(), $config, $model);
		$store = self::storeConfig(self::store(), $config, $model);
		$show = self::showConfig(self::show(), $config, $model);
		$edit = self::editConfig(self::edit(), $config, $model);
		$update = self::updateConfig(self::update(), $config, $model);
		$destroy = self::destroyConfig(self::destroy(), $config, $model);
		$commonConfig = [
			'namespace' => $config->namespace,// namespace
			'controllerName' => $config->fileName,// controllerName
			'modelName' => lcfirst($model),// modelName 首字母小写
			'modelNameUcfirst' => ucfirst($model),// modelNameUcfirst 首字母大写
			'index' => $index,// index
			'create' => $create,// create
			'store' => $store,// store
			'show' => $show,// show
			'edit' => $edit,// edit
			'update' => $update,// update
			'destroy' => $destroy,// destroy
		];
		$data = self::commonConfig($data, $commonConfig);
		
//		echo $data;
		return $data;
	}
}