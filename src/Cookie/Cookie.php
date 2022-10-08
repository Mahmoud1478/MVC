<?php

namespace Src\Cookie;

class Cookie
{

    static function has(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    static function get(string $key)
    {
        return static::has($key) ?? $_COOKIE[$key];
    }

    static function set(string $key, mixed $value, float $expire = 9999)
    {
        setcookie($key, $value, time() + ($expire * 30 * 24 * 60 * 60), '/', '', false, true);
        return $value;
    }

    static function remove(string $key): string
    {
        static::set($key, '', -999);
        return $key;
    }
}