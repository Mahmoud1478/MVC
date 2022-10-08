<?php

namespace Src\Http;


class Server
{


    public static function has(string $key): bool
    {
        return isset($_SERVER[$key]);
    }

    public static function get(string $key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }

    public static function path_info($path): array
    {
        return pathinfo($path);
    }

    public static function all(): array
    {
        return $_SERVER;
    }


    public static function baseUrl(): string
    {
        return static::get('REQUEST_SCHEME').'://'.static::get('HTTP_HOST').static::scriptDir();
    }

    public static function url(?string $path = null) :string
    {
        return static::baseUrl() . $path;
    }

    public static function scriptDir(): string
    {
        return trim(strtolower(dirname(static::get('SCRIPT_NAME'))), '\\');
    }
}