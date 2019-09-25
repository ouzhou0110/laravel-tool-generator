<?php


namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route;


use OuZhou\LaravelToolGenerator\Console\Commands\CommonTrait;

class OldCommonRouter
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
	 * Function: initializationModeSon
	 * Notes: 子权限路由模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  15:52
	 * @return string
	 */
	protected static function initializationModeSon(): string
	{
		return <<<CODE
<?php

#Danger:如果需要使用 Joker_oz 的方法，请勿删除带有 # 标记行
#override tag：@{override}

#@{tag}
Route::group(['namespace' => '@{namespace}','prefix' => '@{prefix}', 'middleware' => ''],function () {
	@{webSonInject2}@{namespace}
	
});

CODE;
	
	}
	
	/**
	 * Function: initializationModeSon1
	 * Notes: 一级注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  10:26
	 * @return string
	 */
	protected static function initializationModeSon1(): string
	{
		return <<<CODE
@{injectWay1}

		@{tag}
		Route::resource('@{routerName}', '@{routerController}');
CODE;
	
	}
	
	/**
	 * Function: initializationModeSon2
	 * Notes: 二级注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  16:08
	 * @return string
	 */
	protected static function initializationModeSon2(): string
	{
		return <<<CODE
@{fatherCode}

	#@{tag}@{namespace}
	Route::group(['namespace' => '@{namespace}','prefix' => '@{prefix}', 'middleware' => ''], function () {
		@{webSonInject3}@{namespace}
	});
	
CODE;
	
	}
	
	/**
	 * Function: initializationModeSon3
	 * Notes: 三级注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  16:27
	 * @return string
	 */
	protected static function initializationModeSon3(): string
	{
		return <<<CODE
@{fatherCode}

		#@{tag}@@{name}
		Route::resource('@{routerName}', '@{routerController}');
CODE;
	
	}
	
	/**
	 * Function: initializationModeSonFilePath
	 * Notes: 向一级注入二级的文件路径
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  10:13
	 * @return string
	 */
	protected static function initializationModeSonFilePath(): string
	{
		return <<<CODE
@{webSonInject}
	
		#@{tag}
		require '@{path}';
CODE;
	
	}
	
	/**
	 * Function: initSon
	 * Notes: 初始化子级路由
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  16:51
	 * @param $config
	 * @param $path
	 * @return string
	 */
	protected static function initSon($config, $path)
	{
		// 1. 加载初始化模板
		$model = self::initializationModeSon();
		$model = str_replace([
			'@{override}', // 注入标识
			'@{tag}', // 父级标识
			'@{namespace}', // 文件路径
			'@{prefix}', // 路由前缀
			'@{webSonInject2}', // 儿子注入标识
		], [
			self::WEB_SON_CREATED,
			$config->filePath,
			$config->filePath,
			lcfirst($config->filePath),
			self::INJECT_TAG,
		], $model);
		// 二级的标识
		$twoCode = self::INJECT_TAG . $config->filePath;
		$twoTag = '@' . $config->filePath;
		
		// 注入儿子
		$son = self::initializationModeSon2();
		$son = str_replace([
			'@{fatherCode}', // 父级标识
			'@{tag}', // 父级tag
			'@{namespace}', // 文件路径
			'@{prefix}', // 路由前缀
			'@{webSonInject3}', // 儿子注入标识
		], [
			$twoCode,
			$twoTag,
			'',
			'',
			self::INJECT_TAG,
		], $son);
		
		// 三级注入
		$threeTag = "$twoTag@Joker";
		$son = self::injectLastRouter($son, $threeTag, $config);
		
		// 替换一级
		$model = str_replace($twoCode, $son, $model);
		echo $model;
		
		// 保存文件
		return self::save($path, $model);
	}
	
	
	/**
	 * Function: injectLastRouter
	 * Notes: 注入最后一级的方法
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-23  18:18
	 * @param $model 父级数据 也是需要返回的数据
	 * @param $tag 自身的标记
	 * @param $config 配置
	 * @return mixed
	 */
	protected static function injectLastRouter($model, $tag, $config)
	{
		$data = self::initializationModeSon3();
		$data = str_replace([
			'@{fatherCode}', // 父级标识
			'@{tag}', // 父级标识
			'@{name}', // 功能名称
			'@{routerName}', // 路由前缀
			'@{routerController}', // 控制器名称
		], [
			self::INJECT_TAG,
			$tag,
			$config->controllerPrefix,
			lcfirst($config->controllerPrefix),
			$config->fileName,
		], $data);
		
		// 数据替换
		return str_replace(self::INJECT_TAG, $data, $model);
	}
	
	/**
	 * Function: injectLevel2FilePathToLevel1
	 * Notes: 将二级路由的注入到一级路由的引用中
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-24  10:29
	 * @param $config
	 * @param $aimPath
	 * @return bool|string
	 */
	protected static function injectLevel2FilePathToLevel1($config, $aimPath)
	{
		$path = base_path('routes/web.php');
		$data = file_get_contents($path);
		
		if (false === strpos($data, self::WEB_SON_CREATED)) {
			echo "Danger：web.php 的自动注入标识被删除，无法注入，请手动注入（require" . "'$aimPath'" . PHP_EOL;
			return false;
		}
		$model = self::initializationModeSonFilePath();
		$tag = "@Joker@$aimPath";
		if (false !== strpos($data, $tag)) {
			return false;
		}
		$model = str_replace([
			'@{webSonInject}', // 注入标识
			'@{tag}', // 自身唯一标识
			'@{path}', // 文件路径
		], [
			self::WEB_SON_CREATED,
			$tag,
			$aimPath
		], $model);
		
		// 替换
		$data = str_replace(self::WEB_SON_CREATED, $model, $data);
		return self::save($path, $data, 0, true);
		
	}
	
	
}