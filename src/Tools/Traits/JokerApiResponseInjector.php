<?php

namespace OuZhou\LaravelToolGenerator\Tools\Traits;

use OuZhou\LaravelToolGenerator\Tools\Classes\JokerApiResponse;

trait JokerApiResponseInjector
{
	private static $singleton;
	/**
	 * @return JokerApiResponse
	 */
	public function api()
	{
		if (!self::$singleton instanceof JokerApiResponse) {
			self::$singleton = new JokerApiResponse();
		}
		return self::$singleton;
	}
}