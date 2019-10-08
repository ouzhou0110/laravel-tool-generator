<?php
namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller;

use OuZhou\LaravelToolGenerator\Console\Commands\CommonTrait;

class SimpleController extends Templates
{
	use CommonTrait;
	/**
	 * Function: generator
	 * Notes: 模型生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  15:28
	 * @param $config
	 * @param $model
	 * @return bool
	 */
	public static function generator($config, $model)
	{
//		var_dump($config);
//		echo $model . PHP_EOL;
//		echo 'sssssss' . PHP_EOL;
		$data = self::simpleControllerMode($config, $model);
		
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
	private static function simpleControllerMode($config, $model):string
	{
		// 初始化配置
		$commonConfig = [
			'namespace' => $config->namespace,// namespace
			'controllerName' => $config->fileName,// controllerName
			'modelName' => lcfirst($model),// modelName 首字母小写
			'modelNameUcfirst' => ucfirst($model),// modelNameUcfirst 首字母大写
			'index' => '',// index
			'create' => '',// create
			'store' => '',// store
			'show' => '',// show
			'edit' => '',// edit
			'update' => '',// update
			'destroy' => '',// destroy
		];
		if ($model) {
			// 获取模型
			$data = self::common();
			$data = self::commonConfig($data, $commonConfig);
		} else {
			// 获取模型
			$data = self::commonWithoutModel();
			$data = self::commonConfigWithoutModel($data, $commonConfig);
		}
//		echo $data;
		return $data;
	}
}