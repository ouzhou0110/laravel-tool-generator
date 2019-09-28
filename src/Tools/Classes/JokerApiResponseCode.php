<?php


namespace OuZhou\LaravelToolGenerator\Tools\Classes;


class JokerApiResponseCode
{

	const SUCCESS = 200;// 请求成功 表示请求成功
	const INVALID_IDENTITY = 401;// 身份无效 表示登录身份失效
	const NOT_FOUND = 404;// 没有数据 表示数据不存在或者没有更多的数据
	const TOO_MANY_REQUEST = 429;// 请求太快了 表示发送了太多的请求
	const SERVER_ERROR = 500;// 服务器错误 表示服务器内部出现异常
	const BAD_GATEWAY = 502;// 网关出现问题 表示网关出现问题一般不会使用
	const REQUEST_TIME = 408;// 请求第三方网关超时 表示请求第三方服务的API超时
	const NOT_ACCEPTABLE = 406;// 请求参数需要协商 表示请求参数变动需要协商修改
	const BAD_REQUEST = 400;// 请求参数不正确 表示请求参数错误
	const CREATED = 201;// 资源创建成功 表示资源创建成功
	const APPLY = 201200;// 资源创建成功 表示资源创建成功
	const CONFLICT = 409;// 表示数据存在冲突 表示数据已存在
	const UNPROCESSABLE_ENTITY = 422;// 请求格正确  表示数据不正确
	const NO_AUTHORITY = 403;// 表示没有访问权限
}