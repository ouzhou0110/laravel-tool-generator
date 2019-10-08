<?php

return [
	/**
	 * 是否根据规则重写route，在生成controller的时候
	 */
	'route' => true,
	
	/*************下方为一次性注入功能，并未太大作用，忽略就行，之后为了之后优化先放在这里*************/
	/**
	 * 是否自动注入跨域功能--middleware
	 */
	'enableCross' => true,
	
	/**
	 * 是否自动注入登录认证功能--middleware
	 */
	'auth' => true,
	
	/**
	 * 是否自动注入系统日志功能--middleware
	 */
	'systemLog' => true,
	
	/**
	 * 是否自动生成模型配置
	 */
	'modelGenerator' => true,
];