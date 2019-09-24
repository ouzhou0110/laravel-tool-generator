<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands;

trait CommonTrait
{
	
	/**
	 * Function: getConfigInfo
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  15:19
	 * @param $baseName
	 * @return Object
	 */
	protected static function getConfigInfo($baseName)
	{
		// 信息数组
		$arr = preg_split('/["\\/","\\\"]/', $baseName);
		
		// 文件前缀
		$firstPrefix = strtolower(array_first($arr));
		// 文件名
		$fileName = ucfirst(end($arr));
		// 路径级别
		$level = count($arr);
		
		// 文件路径
		if ($level > 1) { // 如果指定文件路径就删除文件名
			unset($arr[array_key_last($arr)]);
			$filePath = implode('/', $arr);
			$namespace = '\\' . implode('\\', $arr);
		} else {
			$filePath = ''; // 使用默认路径
			$namespace = '';
		}
		
		// 如果是控制器，拆开获取前缀
		$controllerPrefix = ''; // 控制器前缀
		if (false !== strpos($fileName,'Controller')) {
			$controllerPrefix = str_replace('Controller', '', $fileName);
		}
		
		// 模型名称--驼峰转化为下滑线
		// 根据大写替换成特殊字符,并保留大写字母
		$tableName = preg_replace('/([A-Z])/', '_\\1', $fileName);
		// 去除第一个特殊字符
		$tableName = substr($tableName, 1);
		// 转化为小写
		$tableName = strtolower($tableName);
		
		// 生成路由模板需要的数据
		$arr[] = $controllerPrefix;
//		$arr = array_values($arr); // 重置索引
		return (object)[
			'filePath' => $filePath, // 文件路径
			'fileName' => $fileName, // 文件名
			'tableName' => $tableName, // 表名
			'namespace' => $namespace, // 命名空间
			'firstPrefix' => $firstPrefix, // 第一个前缀  api or web
			'realPath' => $arr, // 用于路由分组使用
			'controllerPrefix' => $controllerPrefix, // 控制器前缀
			'level' => $level, // 路径级别，用于分级分配路由
		];
	}
	
	
	/**
	 * Function: saveToFile
	 * Notes:
	 * User: Joker-oz
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-09  15:34
	 * @param $file
	 * @param $code
	 * @param int $mode 默认模型：0覆盖 8追加
	 * @param bool $isOverride 文件存在是否重写，默认不重写
	 * @return string
	 */
	protected static function save($file, $code, int $mode = FILE_TEXT, bool $isOverride = false)
	{
		$dir = dirname($file);
		if (!$isOverride && file_exists($file)) {
			echo 'Exists => Path: "' . $file . '"' . PHP_EOL;
			return false;
		}
		if (!file_exists($dir)) {
			if (!mkdir($dir, 777, true) && !is_dir($dir)) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
			}
		}
		$result = file_put_contents($file, $code, $mode);
		if ($result) {
			echo 'Success => Path: "' . $file . '"' . PHP_EOL;
			return true;
		}
		echo 'Fail => Path: "' . $file . '"' . PHP_EOL;
		return false;
	}
}