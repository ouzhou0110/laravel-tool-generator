<?php
namespace OuZhou\LaravelToolGenerator\Tools\Classes;

use Illuminate\Http\Response;

/**
 * Class ApiResponse
 * @package App\Tools\Response
 */
class JokerApiResponse
{
	// 返回的 code
	protected $code = 200;
	// 返回msg
	protected $msg = null;
	// 返回的数据，当 H5 为 True 时 返回一个有效的页面 而不是 json 对象
	protected $data = null;
	// HTTP CODE 目前为固定的 200 如果条件允许 请严格按照相关标准返回
	protected $http_code = 200;
	// 返回的 header 头
	protected $headers = [];
	// 是否原生页面
	protected $native = false;
	// H5
	protected $H5 = false;
	
	/**
	 * @return Response
	 */
	public function response()
	{
		if ($this->isNative()) {
			$content = $this->getData();
		} else {
			$content = [
				'status' => $this->getCode(),
				'code' => $this->getCode(),
				'msg' => $this->getMsg(),
				'data' => $this->getData(),
			];
		}
		$response = Response::create(
			$content, $this->getHttpCode()
			, $this->getHeaders()
		);
		return $response;
	}
	
	/**
	 * @return bool
	 */
	public function isNative(): bool
	{
		return $this->native;
	}
	
	/**
	 * @param bool $native
	 * @return ApiResponse
	 */
	public function setNative(bool $native)
	{
		$this->native = $native;
		return $this;
		
	}
	
	/**
	 * @return null
	 */
	public function getData()
	{
		if (request()->input('platform') === 'android' && !$this->isNative()) {
			return ['list' => $this->data];
		}
		return $this->data;
	}
	
	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getCode(): int
	{
		return $this->code;
	}
	
	/**
	 * @param $code
	 * @return $this
	 */
	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}
	
	/**
	 * @return null
	 */
	public function getMsg()
	{
		return $this->msg;
	}
	
	/**
	 * @param $msg
	 * @return $this
	 */
	public function setMsg($msg)
	{
		$this->msg = $msg;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getHttpCode(): int
	{
		return $this->http_code;
	}
	
	/**
	 * @param int $http_code
	 * @return $this
	 */
	public function setHttpCode($http_code = 200)
	{
		$this->http_code = $http_code;
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}
	
	/**
	 * @param array $headers
	 * @return ApiResponse
	 */
	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
		return $this;
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function fail($msg = '操作失败!')
	{
		return $this->setCode(JokerApiResponseCode::NOT_ACCEPTABLE)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function failApply($msg = '操作失败!')
	{
		return $this->setCode(JokerApiResponseCode::APPLY)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param null $data
	 * @param string $msg
	 * @return Response
	 */
	public function success($data = null, $msg = '操作成功!')
	{
		return $this
			->setCode(JokerApiResponseCode::SUCCESS)
			->setMsg($msg)
			->setData($data)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function notFound($msg = '内容不存在!')
	{
		return $this->setCode(JokerApiResponseCode::NOT_FOUND)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function invalidToken($msg = '身份失效,请重新登录!')
	{
		return $this->setCode(JokerApiResponseCode::INVALID_IDENTITY)
			->setMsg($msg)
			->response();
	}
	
	public function noAuthority($msg = '您的权限不足，无法访问！')
	{
		return $this->setCode(JokerApiResponseCode::NO_AUTHORITY)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function paramsError($msg = '参数错误!')
	{
		return $this->setCode(JokerApiResponseCode::BAD_REQUEST)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function exists($msg = '数据已重复!')
	{
		return $this->setCode(JokerApiResponseCode::CONFLICT)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function existed($msg = '内容已存在')
	{
		return $this->setCode(JokerApiResponseCode::CONFLICT)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param string $msg
	 * @return Response
	 */
	public function error($msg = '服务器错误!')
	{
		return $this->setCode(JokerApiResponseCode::SERVER_ERROR)
			->setMsg($msg)
			->response();
	}
	
	/**
	 * @param null $content
	 * @return $this|Response
	 */
	public function native($content = null)
	{
		$this->native = true;
		if (!is_null($content)) {
			return $this
				->setData($content)
				->response();
		}
		return $this;
	}
	
	public function H5()
	{
		$this->H5 = true;
	}
	
	/**
	 * @return bool
	 */
	public function isH5(): bool
	{
		return $this->H5;
	}
	
	/**
	 * @param bool $H5
	 * @return ApiResponse
	 */
	public function setH5(bool $H5)
	{
		$this->H5 = $H5;
		return $this;
	}
}