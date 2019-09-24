<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route;

class WebRouter extends CommonRouter
{
	/**
	 * Function: webGenerator
	 * Notes: web路由生成器
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-20  15:31
	 * @param $config
	 */
	public static function generator($config)
	{
		$filePath = base_path('routes/web.php');
		// 获取数据
		file_exists($filePath) && $data = file_get_contents($filePath);
		// 检测是否被重写
		if (false === strpos($data, self::WEB_KEY)) {
			// 加载初始化模板
			self::init(self::initializationMode());
		}
		// 加载追加逻辑
		self::append($config);
		
	}
	
	/**
	 * Function: init
	 * Notes: 初始化web路由
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  9:29
	 * @param string $model
	 */
	private static function init(string $model)
	{
		// 替换相关信息
		$model = str_replace([
			'@{override}', // 替换注入标识
			'@{prefix}', // 替换路由前缀
			'@{namespace}', // 替换主命名空间
			'@{injectWay1}', // 替换注入标识
			'@{webSonInject}', // 子权限标识注入
		], [
			self::WEB_KEY,
			'web',
			'Web',
			self::INJECT_WAY_1, // 一级路由的注入
			self::WEB_SON_CREATED, // 二级路由注入定位符
		], $model);
		
		// 保存文件
		$path = base_path('routes/web.php');
		self::save($path, $model, 0, true);
	}
	
	/**
	 * Function: append
	 * Notes: 向模型中追加或者生成路由配置
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  9:32
	 * @param $config 配置信息
	 * @return bool|void
	 */
	private static function append($config)
	{
		var_dump($config);
		// 注入方式1：UserController 这样的 controller注入
		if ($config->level == 1) {
			return self::append1($config);
		}
		
		if ($config->level == 2) {
			// 注入方式2：Admin/UserController 这样的 controller注入
			return self::append2($config);
		}

		// 注入方式3: Admin/System/IndexController 这样的 controller注入
		if ($config->firstPrefix === 'web' && $config->level == 3) {
			// 方式2注入
			// 避免大小写错误--首字母统统小写
			$config->filePath = lcfirst($config->filePath);
			// 去掉web
			$config->filePath = str_replace('web/', '', $config->filePath);
			unset($config->realPath[0]);
			--$config->level;
			return self::append2($config);
		}
	}
	
	/**
	 * Function: append1
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  10:38
	 * @param $config
	 * @return bool
	 */
	private static function append1($config): bool
	{
		// 1，检测是否被初始化了
		$path = base_path('routes/web.php');
		$data = file_get_contents($path);
		if (false === strpos($data, self::INJECT_WAY_1)) {
			// 模型不存在或者被删除，放弃追加
			echo 'Fail: 指定的标识已经被删除，无法执行操作。标识：' . self::INJECT_WAY_1 . PHP_EOL;
			return false;
		}
		
		// 正式追加
		// 1. 获取追加数据模型
		$model = self::initializationModeSon1();
		// 2. 替换数据
		$tag = '#@Joker@' . $config->controllerPrefix;

		// 检测是否已经存在
		if (false !== strpos($data, $tag)) {
			echo "Fail: 标记为 => $tag 的路由已经存在";
			return false;
		}
		$model = str_replace([
			'@{tag}', // 标识
			'@{injectWay1}', // 注入标识修改
			'@{routerName}', // 路由名称
			'@{routerController}', // 所关联控制器
		], [
			$tag,
			self::INJECT_WAY_1,
			lcfirst($config->controllerPrefix),
			$config->fileName,
		], $model);
		
		// 注入到指定位置
		$data = str_replace(self::INJECT_WAY_1, $model, $data);
		return self::save($path, $data, FILE_TEXT, true); // 重写文件
	}
	
	/**
	 * Function: append2
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  10:05
	 * @param $config
	 * @return bool|string
	 */
	private static function append2($config)
	{
		// 检测第一级是不是 web
		if ($config->firstPrefix == 'web') {
			// 1. 是，调用 append1 表示为一级路由
			return self::append1($config);
		}
		var_dump($config);
		// 2. 否，检测 对应的第一级文件是否存在: user.php
		$path = base_path('routes/' . self::WEB_SON_BASE_PATH . $config->firstPrefix . '.php');
		if (!file_exists($path)) {
			// 文件不存在，调用初始化程序
			return self::initSon($config, $path);
		}
		// 文件存在
		$model = file_get_contents($path);
		// 检测标识是否存在
		$aimTag = '#@' . $config->filePath . '@Joker@' . $config->controllerPrefix;
		
		if (false === strpos($model, $aimTag)) {
			// 没有添加对应信息
			$tag = '@' . $config->filePath . '@Joker';
			$model = self::injectLastRouter($model, $tag, $config);
			return self::save($path, $model, 0, true);
		}
		// 想想应该不会出现，因为此时控制器肯定重复了
		echo "Fail: 标记为 => $aimTag 的路由已经存在";
		return false;
	}
	
}