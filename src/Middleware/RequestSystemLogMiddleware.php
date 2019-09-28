<?php
namespace OuZhou\LaravelToolGenerator\Middleware;

use Closure;
use OuZhou\LaravelToolGenerator\Tools\StaticClasses\JokerLog;

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
	    JokerLog::writeSystemLogs();
        return $next($request);
	}
}