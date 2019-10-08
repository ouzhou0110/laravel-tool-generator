<?php


namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller;


use OuZhou\LaravelToolGenerator\Console\Commands\ModelCRUDCommand;

class TemplatesConfig
{
	/**
	 * Function: commonConfig
	 * Notes: 通用模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  11:36
	 * @param $data
	 * @param $config
	 * @return mixed
	 */
	protected static function commonConfig($data, $config)
	{
		$data = str_replace([
			'@{actionBasePath}', // action注入
			'@{repositoryBasePath}', // repository注入
			'@{serviceBasePath}', // service注入
			'@{namespace}',// namespace
			'@{controllerName}',// controllerName
			'@{modelName}',// modelName 首字母小写
			'@{modelNameUcfirst}',// modelNameUcfirst 首字母大写
			'@{index}',// index
			'@{create}',// create
			'@{store}',// store
			'@{show}',// show
			'@{edit}',// edit
			'@{update}',// update
			'@{destroy}',// destroy
			
		], [
			ModelCRUDCommand::ACTION_NAMESPACE . '\\',
			ModelCRUDCommand::REPOSITORY_NAMESPACE . '\\',
			ModelCRUDCommand::SERVICE_NAMESPACE . '\\',
			$config['namespace'],
			$config['controllerName'],
			$config['modelName'],
			$config['modelNameUcfirst'],
			$config['index'],
			$config['create'],
			$config['store'],
			$config['show'],
			$config['edit'],
			$config['update'],
			$config['destroy'],
		] , $data);
		
		return $data;
	}
	
	/**
	 * Function: commonConfigWithoutModel
	 * Notes: 通用模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  11:36
	 * @param $data
	 * @param $config
	 * @return mixed
	 */
	protected static function commonConfigWithoutModel($data, $config)
	{
		$data = str_replace([
			'@{namespace}',// namespace
			'@{controllerName}',// controllerName
			'@{index}',// index
			'@{create}',// create
			'@{store}',// store
			'@{show}',// show
			'@{edit}',// edit
			'@{update}',// update
			'@{destroy}',// destroy
			
		], [
			$config['namespace'],
			$config['controllerName'],
			$config['index'],
			$config['create'],
			$config['store'],
			$config['show'],
			$config['edit'],
			$config['update'],
			$config['destroy'],
		] , $data);
		
		return $data;
	}
	
	/**
	 * Function: indexConfig
	 * Notes: index 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function indexConfig($data, $config, $model)
	{
		$data = str_replace([
			'@{modelName}', // modelName
		], [
			lcfirst($model)
		], $data);
		
		return $data;
		
	}
	
	/**
	 * Function: createConfig
	 * Notes: create 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function createConfig($data, $config, $model)
	{
//		$data = str_replace([
//			'@{modelName}', // modelName
//		], [
//			lcfirst($model)
//		], $data);
		
		return $data;
		
	}
	
	/**
	 * Function: storeConfig
	 * Notes: store 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function storeConfig($data, $config, $model)
	{
		$data = str_replace([
			'@{modelName}', // modelName
		], [
			lcfirst($model)
		], $data);
		
		return $data;
	}
	
	/**
	 * Function: showConfig
	 * Notes: show 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function showConfig($data, $config, $model)
	{
		$data = str_replace([
			'@{modelName}', // modelName
		], [
			lcfirst($model)
		], $data);
		
		return $data;
	}
	
	/**
	 * Function: editConfig
	 * Notes: edit 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function editConfig($data, $config, $model)
	{
//		$data = str_replace([
//			'@{modelName}', // modelName
//		], [
//			lcfirst($model)
//		], $data);
		
		return $data;
	}
	
	/**
	 * Function: updateConfig
	 * Notes: update 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function updateConfig($data, $config, $model)
	{
		$data = str_replace([
			'@{modelName}', // modelName
		], [
			lcfirst($model)
		], $data);
		
		return $data;
	}
	
	/**
	 * Function: destroyConfig
	 * Notes: destroy 模型配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:06
	 * @param $data
	 * @param $config
	 * @param $model
	 * @return mixed
	 */
	protected static function destroyConfig($data, $config, $model)
	{
		$data = str_replace([
			'@{modelName}', // modelName
		], [
			lcfirst($model)
		], $data);
		
		return $data;
	}
}