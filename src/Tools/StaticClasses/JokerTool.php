<?php

namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

class JokerTool
{
	/**
	 * Function: dateAddTimestamp
	 * Notes: 指定时间加上时间戳，返回指定时间
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-19  9:53
	 * @param $date
	 * @param $timestamp
	 * @return false|string
	 */
	public static function dateAddTimestamp($date, $timestamp, int $addSecond = 0)
	{
		// date 转 timestamp
		$temp = strtotime($date);
		// 加上附加的时间戳
		$temp += $timestamp + $addSecond;
		// 转化为dateTime
		return date('Y-m-d H:i:s', $temp);
	}
	
	/**
	 * Function: stringReplaceWithTag
	 * Notes: 字符串替换
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-15  9:44
	 * @param string $string
	 * @param int $start 开始位置 大于0
	 * @param int $end 自然数
	 * @param string $tag
	 * @return mixed
	 * @throws \Exception
	 */
	public static function JokerStringReplaceWithTag(string $string, int $start, int $end = 0, string $tag = '*')
	{
		// 验证字符串长度是否小于开始位置
		$strlen = mb_strlen($string);
		// 开始位置必须为正数
		if ($start < 0) {
			throw new \Exception('开始位置必须为正数！');
		}
		if ($strlen < $start) {
			throw new \Exception('开始位置必须小于字符串长度！');
		}
		// 验证字符串长度是否小于结束位置
		if ($end > 0 && $strlen < $end) {
			throw  new \Exception('结束位置必须小于字符串长度！');
		}
		// 当end为负数时--验证范围
		if ($strlen + $end < 0) {
			throw  new \Exception('结束位置超出字符串长度范围！');
		}
		// 验证开始位置是否大于结束位置 -- 结束位置
		if ($end > 0 && $start > $end) {
			throw  new \Exception('开始位置必须小于结束位置！');
		}
		// 结束位置为正数--获取需要截取的长度
		if ($end > 0) {
			$len = $end - $start;
		} else {
			$len = $strlen + $end - $start;
		}
		
		try {
			// 替换字符串
			return substr_replace($string, str_repeat($tag, $len), $start, $len);
		} catch (\Exception $e) {
			throw new \Exception('字符串替换异常，请检查参数');
		}
	}
	
	/**
	 * Function: getNowWithDateTime
	 * Notes: 获取当前时间 y-m-d h-i-s
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  12:01
	 * @return false|string
	 */
	public static function getNowWithDateTime()
	{
		return date('Y-m-d H:i:s');
	}
	
	/**
	 * Function: getNowWithDate
	 * Notes: 获取当前日期 y-m-d
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  12:01
	 * @return false|string
	 */
	public static function getNowWithDate()
	{
		return date('Y-m-d');
	}
	
	/**
	 * Function: moneyRegex
	 * Notes:
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-08  17:46
	 * @param $money
	 * @throws \Exception
	 */
	public static function moneyRegex($money)
	{
		$reg = '/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/';
		if (preg_match($reg, $money) != 0) {
			return;
		}
		throw new \Exception('金额的格式错误', 400);
	}
	
	/**
	 * Function: phoneRegex
	 * Notes: 电话号码验证
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-26  10:54
	 * @param string $phone
	 * @return bool
	 * @throws \Exception
	 */
	public static function phoneRegex(string $phone)
	{
		$reg = '/^(1[3-9])\d{9}$|^(400+\d{7})$|^(800+\d{7})$|^(0+\d{2,3}-+\d{8})$/';
		if (preg_match_all($reg, $phone)) {
			return true;
		}
		throw new \Exception('电话不符合规范');
	}
	
	
	/**
	 * Function: JokerBasePaginate
	 * Notes: Joker 自定义分页实现
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-08-27  16:10
	 * @param \Illuminate\Database\Query\Builder $builder
	 * @param int $pageSize
	 * @param int $page
	 * @return array
	 */
	public static function basePaginate(\Illuminate\Database\Query\Builder $builder, int $pageSize = 15, int $page = 1): array
	{
		$listQuery = $builder; // 每页数据query
		
		$totalQuery = clone $builder; // 总数query
		
		$list = $listQuery->offset(($page - 1) * $pageSize)->limit($pageSize)->latest()->get(); // 每页数量
		
		$total = $totalQuery->count('id'); // 总数
		return [
			'pageSize' => $pageSize,
			'page' => $page,
			'total' => $total,
			'list' => $list
		];
	}
	
}