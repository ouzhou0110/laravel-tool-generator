<?php

namespace OuZhou\LaravelToolGenerator\Auth;

use App\Http\JokerAuth\AuthCookie;
use App\Http\JokerAuth\AuthToken;
use Cookie;

class JokerAuth
{
    /**
     * @var $cookie 以cookie作为登录标识
     */
	protected $cookie;

    /**
     * @var $cookie 以token作为登录标识
     */
	protected $token;

	public function __construct()
    {

        $this->cookie = new AuthCookie();
        $this->token = new AuthToken();
    }

    public function __call($name, $arguments)
    {
        if (strtolower(env('LOGIN_METHOD')) === 'cookie') {
                return $this->cookie->$name(...$arguments);
        }
        return $this->cookie->$name(...$arguments);
    }

}