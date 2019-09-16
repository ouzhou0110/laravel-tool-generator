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
		$response->headers->set('Access-Control-Allow-Origin', $request->header('Origin')); // laravel 5.6版本以上
		$response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json, x-xsrf-token');
		$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
		$response->headers->set('Access-Control-Allow-Credentials', 'true');
		return $response;
	}
}