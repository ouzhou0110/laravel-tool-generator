<?php


namespace OuZhou\LaravelToolGenerator\Exceptions;


use Throwable;

class NotFoundFileException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		empty($message) && $message = '请选择要上传的文件!';
		parent::__construct($message, $code, $previous);
	}
}