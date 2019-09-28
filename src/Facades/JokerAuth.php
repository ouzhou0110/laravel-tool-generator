<?php


namespace OuZhou\LaravelToolGenerator\Facades;


use Illuminate\Support\Facades\Facade;

class JokerAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
       return new \OuZhou\LaravelToolGenerator\Auth\JokerAuth();
    }
}