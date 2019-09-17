<?php

namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

use Cookie;
use Illuminate\Http\Request;

final class JokerAuthByCookie
{
	const COOKIE_NAME = 'user_token';
	
	/**
	 * Function: getCookie
	 * Notes: 返回储存着cookie的token
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-07  17:45
	 * @param $userId
	 * @param string $identity 登录者身份
	 * @param array $data
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 * @throws \Exception
	 */
	public static function login($userId, $identity = '', $identiyNum = 1, $data = []): \Symfony\Component\HttpFoundation\Cookie
	{
		// 存入session
		$secretKey = bcrypt(bcrypt(time() . $userId . random_bytes(20)));
		session([
			$secretKey => [
				'id' => $userId,
				'identity' => $identity,
				'identityNum' => $identiyNum,
				'data' => $data
			]
		]);
		return Cookie::make(self::COOKIE_NAME, $secretKey);
	}
	
	/**
	 * Function: logout
	 * Notes: 退出登录
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-07  17:55
	 * @param Request $request
	 * @return bool
	 */
	public static function logout(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			session()->forget($key);
			return true;
		}
		return false;
	}
	
	/**
	 * Function: checkLogin
	 * Notes: 检查是否登录了或者登录失败
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-07  17:50
	 * @param Request $request
	 * @return bool
	 */
	public static function checkLogin(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			if (session($key)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Function: getUserId
	 * Notes: 返回登录者id
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-07  18:24
	 * @param Request $request
	 * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|null
	 */
	public static function getUserId(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			if ($temp = session($key)) {
				return $temp['id'];
			}
		}
		return null;
	}
	
	/**
	 * Function: getUser
	 * Notes: 返回登录者信息
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-12  17:16
	 * @param Request $request
	 * @return |null
	 */
	public static function getUser(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			if ($temp = session($key)) {
				return $temp['data'];
			}
		}
		return null;
	}
	
	/**
	 * Function: getIdentity
	 * Notes: 返回登录者身份
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  11:14
	 * @param Request $request
	 * @return |null
	 */
	public static function getIdentity(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			if ($temp = session($key)) {
				return $temp['identity'];
			}
		}
		return null;
	}
	
	/**
	 * Function: getIdentityNum
	 * Notes: 返回登录者身份 number
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-12  12:07
	 * @param Request $request
	 * @return int
	 */
	public static function getIdentityNum(Request $request)
	{
		if ($key = $request->cookie(self::COOKIE_NAME)) {
			if ($temp = session($key)) {
				return $temp['identityNum'];
			}
		}
		return 0;
	}
	
}