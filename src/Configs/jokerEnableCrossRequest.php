<?php

return [
	/*
	 * 允许的请求头
	 */
	'headers' => 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json, x-xsrf-token, x-joker-token, X-JOKER-TOKEN',
	
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
//	'origin' => '*',
	'origin' => 'http;//127.0.0.1:8080', // vue默认路径
];