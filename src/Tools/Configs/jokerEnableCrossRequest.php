<?php

return [
	/*
	 * 允许的请求头
	 */
	'headers' => 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json, x-xsrf-token',
	
	/*
	 * 允许的请求类型
	 */
	'methods' => 'GET, POST, PATCH, PUT, OPTIONS, DELETE',
	
	/*
	 * 是否支持cookie
	 */
	'cookie' => 'true',
	
	/*
	 * 允许的域名
	 * 开启cookie后台，只允许一个访问
	 */
//	'origin' => request()->header('Origin'), // 5.6以上版本
	'origin' => '127.0.0.1:8080', // vue默认路径
];