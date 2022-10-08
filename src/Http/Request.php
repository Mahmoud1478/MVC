<?php

namespace Src\Http;

class Request
{

    private static array $query_string = [];

    public static function handle(): void
    {

    }


    private static function value(string $key, array $type = null)
    {
        $type = $type ?? $_REQUEST;
        return static::has($type, $key) ? $type[$key] : null;
    }



    public static function queryString(): array
    {
        return static::$query_string;
    }


    public static function method(): string
    {
        return Server::get('REQUEST_METHOD');
    }


    public static function has(array $type, string $key): bool
    {
        return array_key_exists($key, $type);
    }

    public static function get(string $key , mixed $default = null)
    {
        $type = '$_'.static::method();
        return static::value($key, $$type);
    }

    public static function all(): array
    {
//        $type = '$_'.static::method();
//        return $$type;
        return $_REQUEST;
    }

    public function uri(): string
    {
        return urldecode(str_replace(Server::scriptDir() , '', strtolower(Server::get('REQUEST_URI'))));
    }
}
