<?php


namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

use Illuminate\Routing\Route;

class BaseLog
{
	/**
	 * Function: writeSystemLogs
	 * Notes: 访问日志
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:11
	 * @param null $note
	 */
	public static function writeSystemLogs($note = null)
	{
		$request = request();
		//关闭
		if (config('app.log_debug') == false) {
			return;
		}
		//选择日志存放位置
		$data['time'] = date('Y-m-d H:i:s'); // 当前时间
		$data['account_id'] = JokerAuth::getUserId($request); // 操作者账号
		$data['http_code'] = http_response_code(); // 相应状态码
		$data['method'] = $request->method(); // 请求方法
		$data['host_protocol'] = $request->getSchemeAndHttpHost(); // 主机
		$data['uri'] = $request->getUri(); // uri
		$data['action'] = Route::currentRouteAction(); // 请求路由
		$data['request_body'] = json_encode($request->all()); // 请求体--参数
		$data['content_type'] = $request->header('content-type'); // 内容类型
		$data['ip'] = $request->getClientIp(); // 主机ip
		$data['referer'] = $request->server('HTTP_REFERER'); // 请求缘由--可以做防盗链，也可以伪造，所以不太可信
		$data['cookie'] = json_encode($request->cookies->all()); // cookie
		$data['token'] = $request->header('token'); // 令牌
		$data['note'] = $note; // 标记
		$data['user_agent'] = $request->userAgent(); // 用户代理
		/**
		 * 拼接内容
		 */
		$log = '';
		foreach ($data as $k => $item) {
			$log .= "\t". '{'. $k . ' => ' . ($item ?? '#') . '}';
		}
		/**
		 * 写入日志
		 */
		self::writeLogsTo($log, 'system');
		unset($data);
	}
	
	/**
	 * Function: writeResponseLog
	 * Notes: 响应日志
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:11
	 * @param $logs
	 */
	public static function writeResponseLog($logs)
	{
		self::writeLogsTo($logs, 'response');
	}
	
	/**
	 * Function: writePayLog
	 * Notes: 支付日志
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:12
	 * @param $logs
	 */
	public static function writePayLog($logs)
	{
		self::writeLogsTo($logs, 'pay');
	}
	
	/**
	 * Function: writePushLogs
	 * Notes: 推送日志
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:12
	 * @param $logs
	 */
	public static function writePushLogs($logs)
	{
		self::writeLogsTo($logs, 'push');
	}
	
	/**
	 * Function: writeRuntimeLog
	 * Notes: 运行日志
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:12
	 * @param string $data
	 * @return bool|int
	 */
	public static function writeRuntimeLog($data = '')
	{
		return self::writeLogsTo($data, 'runtime');
	}
	
	/**
	 * Function: writeLogsTo
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-17  16:08
	 * @param $logs
	 * @param $path
	 * @return bool|int
	 */
	private static function writeLogsTo($logs, $path)
	{
		
		$logfile = storage_path() . '/logs/' . $path . '/' . date('Ymd') . '.log';
		file_exists(dirname($logfile)) || mkdir(dirname($logfile), 0777, true);
		if (!is_array($logs)) {
			$logs = [$logs];
		}
		
		// 向数组插入环境和路径信息
		array_unshift($logs,	vsprintf('%s.%s', [strtolower(env('APP_ENV')), strtolower($path)]));
		// 插入当前时间
		array_unshift($logs, vsprintf('[%s]', [now()->toDateTimeString()]));
		$log = '';
		foreach ($logs as $item) {
			if (is_array($item) || is_object($item)) {
				$item = json_encode($item);
			}
			$log .= "$item\r\n";
		}
		// 写入数据
		file_put_contents($logfile, $log . PHP_EOL, FILE_APPEND);
	}
}