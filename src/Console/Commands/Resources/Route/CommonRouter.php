<?php


namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route;


use OuZhou\LaravelToolGenerator\Console\Commands\CommonTrait;

class CommonRouter
{
	use CommonTrait;
	
	const WEB_KEY = 'web85a67bc449b060c32637a034162af586';
	const API_KEY = 'api93a67bc879b060ab041de034162af586';
	
	// web的子类是否被初始化
	const WEB_SON_CREATED = '#webSon14300f6011ae057b3c02a7ea2d2d4d5e';
	const WEB_SON_BASE_PATH = 'webRoutes/';
	// api的子类是否被初始化
	const API_SON_BASE_PATH = 'apiRoutes/';
	const API_SON_CREATED = '#apiSon14300f6011ae057b3c02a7ea2d2d4d5e';
	
	// 注入方式1：UserController 这样的 controller注入
	const INJECT_WAY_1 = '#one@injectWay1-dc483e80a7a0bd9ef71d8cf973673924';
	// 注入方式2：Admin/UserController 这样的 controller注入
	const INJECT_WAY_2 = '#two@injectWay2-ec26001d1ff7885b72a834b40862f056';
	// 注入方式3: Admin/System/IndexController 这样的 controller注入
	const INJECT_WAY_3 = '#three@injectWay3-e10adc3949ba59abbe56e057f20f883e';
	
	
	/**
	 * Function: initBaseWebRoute
	 * Notes: 初始化web.php
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  8:58
	 * @return string
	 */
	protected static function initBaseWebRoute(): string
	{
		return <<<CODE
<?php

#Danger:如果需要使用 Joker_oz 的方法，请勿删除带有 # 标记行
#override tag：@{override}

Route::Group(['prefix' => '@{prefix}', 'namespace' => '@{namespace}', 'middleware' => 'apiRequestLog'], function (){
    // 权限通用 -- 不需要登录认证
    Route::group([], function ()
    {
		Route::get('no/login', 'LoginController@noLogin')->name('no.login'); // 没有登录
		
		Route::get('no/authority', 'LoginController@noAuthority')->name('no.authority'); // 没有权限
		
		Route::post('login', 'LoginController@login'); // 登录
		
		Route::delete('logout', 'LoginController@logout'); // 退出登录
	
    });
	
	// 权限通用 -- 需要登录认证
    Route::group(['middleware' => 'apiLogin'], function() {
		Route::get('index', 'LoginController@index'); // 测试
	
		Route::post('upload', 'UploadController@index');//文件上传
		
		Route::post('upload/video', 'UploadController@video');// 视频上传
		
		@{injectWay1}
	});
    
    // 单独权限--加上登录认证
	Route::group(['middleware' => 'apiLogin'], function() {
		@{webSonInject}
	});
 
});

CODE;
	
	}
	
	/**
	 * Function: level1Mode
	 * Notes: web.php 注入使用的模型
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  10:26
	 * @return string
	 *
	 * 例子
	 * #one@injectWay1-dc483e80a7a0bd9ef71d8cf973673924
	 * #@Joker/User
	 * Route::resource('user', 'UserController');
	 */
	protected static function level1Mode(): string
	{
		return <<<CODE
@{injectWay1}

		#@{tag}
		Route::resource('@{routerName}', '@{routerController}');
CODE;
	
	}
	
	/**
	 * Function: level2FileMode
	 * Notes: 二级路由文件的模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:05
	 * @return string
	 */
	protected static function level2FileMode(): string
	{
		return <<<CODE
<?php

#Danger:如果需要使用 Joker_oz 的方法，请勿删除带有 # 标记行
#override tag：@{override}

#@{tag}
Route::group(['namespace' => '@{namespace}', 'prefix' => '@{prefix}', 'middleware' => ''], function () {
	@{injectWay2}@{tag}
	
});

CODE;
	
	}
	
	/**
	 * Function: level2GroupMode
	 * Notes: 二级路由 group 模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:34
	 * @return string
	 *
	 */
	protected static function level2GroupMode()
	{
		return <<<CODE
@{injectTag}

	#@{tag}
	Route::group(['namespace' => '@{namespace}', 'prefix' => '@{prefix}', 'middleware' => ''], function () {
		@{injectWay2}@{tag}
		
	});
CODE;

	}
	
	/**
	 * Function: level2FilePathToWebMode
	 * Notes: 将二级路由文件注入 web.php 的引用中的模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:16
	 * @return string
	 */
	private static function level2FilePathToWebMode(): string
	{
		return <<<CODE
@{webSonInject}
	
		#@{tag}
		require '@{path}';
CODE;
	
	}
	
	/**
	 * Function: lastLevelRouteMode
	 * Notes: 末级路由模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  17:31
	 * @return string
	 */
	private static function lastLevelRouteMode()
	{
		return <<<CODE
@{injectTag}
            
            #@{tag}
            Route::resource('@{routeName}', '@{controller}');
CODE;

	}
	
	/**
	 * Function: injectLevel2FilePathToWeb
	 * Notes: 将二级路由注入到 web.php 的引用中
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:13
	 * @param $filePath web.php or api.php 路径
	 * @param $aimPath 需要注入的路径
	 * @return bool|string
	 *
	 * Admin/UserController
	 */
	protected static function injectLevel2FilePathToWeb($filePath, $aimPath)
	{
		
		$data = file_get_contents($filePath);
		
		if (false === strpos($data, self::WEB_SON_CREATED)) {
			echo "Danger：web.php 的自动注入标识被删除，无法注入，请手动注入（require" . "'$aimPath'" . PHP_EOL;
			return false;
		}
		$model = self::level2FilePathToWebMode();
		$tag = "@Joker/$aimPath";
		if (false !== strpos($data, $tag)) {
			return false;
		}
		$model = str_replace([
			'@{webSonInject}', // 注入标识
			'@{tag}', // 自身唯一标识： #@Joker/webRoutes/admin.php
			'@{path}', // 文件路径 webRoutes/admin.php
		], [
			self::WEB_SON_CREATED,
			$tag,
			$aimPath
		], $model);
		
		// 替换
		$data = str_replace(self::WEB_SON_CREATED, $model, $data);
		return self::save($filePath, $data, 0, true);
		
	}
	
	/**
	 * Function: initLevel2RouteFile
	 * Notes: 出示化二级路由文件
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:07
	 * @param $config
	 * @return string
	 *
	 * Admin/UserController
	 */
	protected static function initLevel2RouteFile($config): string
	{
		// 1. 加载初始化模板
		$model = self::level2FileMode();
		$model = str_replace([
			'@{override}', // 注入标识
			'@{tag}', // 自身标识
			'@{namespace}', // 文件路径
			'@{prefix}', // 路由前缀
			'@{injectWay2}', // 儿子注入标识
		], [
			self::WEB_SON_CREATED,
			'@' . $config->filePath,
			$config->filePath,
			lcfirst($config->filePath),
			self::INJECT_WAY_2,
		], $model);
		
		// 保存文件
		return $model;
	}
	
	/**
	 * Function: initLevel2Group
	 * Notes: 初始化二级路由的 group
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  16:40
	 *
	 * Admin/UserController
	 * @param $injectTag
	 * @param $tag
	 * @param $namespace
	 * @param $prefix
	 * @param $sonInject
	 * @return mixed|string
	 */
	protected static function initLevel2Group($injectTag, $tag, $namespace, $prefix, $sonInject)
	{
		$model = self::level2GroupMode();
		$model = str_replace([
			'@{injectTag}', // 注入地点标识：#two@injectWay2-ec26001d1ff7885b72a834b40862f056@Admin
			'@{tag}', // 自身唯一标识： #@Admin/User
			'@{namespace}', // 命名空间：''
			'@{prefix}', // 路由前缀：''
			'@{injectWay2}', // 子集注入地点：#two@injectWay2-ec26001d1ff7885b72a834b40862f056@Admin/User
		], [
			$injectTag,
			$tag,
			$namespace,
			$prefix,
			$sonInject,
		], $model);
		
		return $model;
	}
	
	/**
	 * Function: initLastLevelRoute
	 * Notes: 初始化末级路由信息 resource
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  17:11
	 * @param $injectTag
	 * @param $tag
	 * @param $routeName
	 * @param $controllerName
	 * @return mixed|string
	 *
	 * Admin/UserController
	 */
	protected static function initLastLevelRoute($injectTag, $tag, $routeName, $controllerName):string
	{
		$model = self::lastLevelRouteMode();
		$model = str_replace([
			'@{injectTag}', // 注入地点标识：#three@injectWay3-e10adc3949ba59abbe56e057f20f883e@Admin/User
			'@{tag}', // 自身唯一标识： #@Admin/User
			'@{routeName}', // 路由名称：user
			'@{controller}', // 控制器名称：UserController
		], [
			$injectTag,
			$tag,
			$routeName,
			$controllerName,
		], $model);
		
		return $model;
		
	}
	
}