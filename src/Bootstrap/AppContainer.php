<?php

namespace Src\Bootstrap;

class AppContainer
{
    private static array $container = [];

    public static function all(): array
    {
        return static::$container;
    }


    public static function get(string $key)
    {
        return static::$container[$key] ?? null;
    }

    public static function set(string $key , string $className ,?array $pram = null) :mixed
    {
        static::$container[$key] = new $className($pram);
        return static::$container[$key];
    }

    public static function setInstance(string $footprint , $instant): void
    {
        static::$container[$footprint] = $instant;
    }
}