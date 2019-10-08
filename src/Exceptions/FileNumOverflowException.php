<?php

namespace OuZhou\LaravelToolGenerator\Exceptions;

use Throwable;
use OuZhou\LaravelToolGenerator\Tools\StaticClasses\BaseFileUploader;


class FileNumOverflowException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		empty($message) && $message = '文件数量超出最大限制' . BaseFileUploader::MAX_NUMBER;
		parent::__construct($message, $code, $previous);
	}
}