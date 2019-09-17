<?php
namespace OuZhou\LaravelToolGenerator\Tools\Middlewares;

use Closure;

class EnableCrossRequestMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$response = $next($request);
		$response->headers->set('Access-Control-Allow-Origin', config('jokerEnableCrossRequest.origin')); // 允许的域名
		$response->headers->set('Access-Control-Allow-Headers', config('jokerEnableCrossRequest.headers')); // 允许的请求头
		$response->headers->set('Access-Control-Allow-Methods', config('jokerEnableCrossRequest.methods')); // 允许的请求类型
		$response->headers->set('Access-Control-Allow-Credentials', config('jokerEnableCrossRequest.cookie')); // 是否支持cookie--默认开启
		return $response;
	}
}