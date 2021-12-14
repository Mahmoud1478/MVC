<?php

namespace Src\Http;

class Server
{

    private function __construct(){}

    public static function has(string $key): bool
    {
        return isset($_SERVER[$key]);
    }

    public static function get(string $key)
    {
        return static::has($key)?$_SERVER[$key]:null;
    }

    public static function path_info($path) :array
    {
        return pathinfo($path);
    }

    public static function all(): array
    {
        return $_SERVER;
    }
}