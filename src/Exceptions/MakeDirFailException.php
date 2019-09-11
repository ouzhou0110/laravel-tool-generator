<?php


namespace OuZhou\LaravelToolGenerator\Exceptions;


use Throwable;

class MakeDirFailException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		empty($message) && $message = '创建目录失败!';
		parent::__construct($message, $code, $previous);
	}
}