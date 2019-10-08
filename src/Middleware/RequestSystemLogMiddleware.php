<?php
namespace OuZhou\LaravelToolGenerator\Middleware;

use Closure;
use OuZhou\LaravelToolGenerator\Tools\StaticClasses\BaseLog;

class RequestSystemLogMiddleware
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
	    BaseLog::writeSystemLogs();
        return $next($request);
	}
}