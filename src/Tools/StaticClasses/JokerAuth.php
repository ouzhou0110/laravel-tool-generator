<?php

namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

use Cookie;

class JokerAuth
{
	/**
	 * Function: __callStatic
	 * Notes: 根据环境调用对应的方法--默认cookie模式
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:19
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		// 使用cookie做登录验证
		if (env('LOGIN_METHOD') == 'cookie' || !env('LOGIN_METHOD')) {
			// 排除getCookie方法--参数特殊处理
			if ($name == 'login') {
				return JokerAuthByCookie::$name(...$arguments);
			}
			return JokerAuthByCookie::$name($arguments[0]);
		} else {
			// 排除getCookie方法--参数特殊处理
			if ($name == 'login') {
				return JokerAuthByToken::$name(...$arguments);
			}
			return JokerAuthByToken::$name($arguments[0]);
		}
	}
	
}