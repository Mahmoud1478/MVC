<?php

namespace Src\Cookie;

use Cassandra\Function_;

class Cookie
{
    private function __construct(){}



    /**
     * @pram  sting $key
     *
     * @return  boolean
     * */
    static function has(string $key){
        return isset($_COOKIE[$key]);
    }

    static function get(string $key){
        return static::has($key)?? $_COOKIE[$key];
    }

    static function set(string $key , $value , float $expire = 9999){
        setcookie($key,$value,time() + ($expire * 30 * 24 * 60 * 60),'/','',false ,true);
        return $value;
    }

    static function remove(string $key){
        static::set($key,'',-999);
        return $key;
    }
}