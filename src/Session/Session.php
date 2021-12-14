<?php

namespace Src\Session;

class Session
{
    private function __construct(){}




    public static function start()
    {
        if (! session_id()){
            ini_set('session.use_only_cookies',1);
            session_start();
        }
    }




    public static function set(string $key, string $value): string
    {
        $_SESSION[$key]= $value;
        return $value;
    }




    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }




    public static function get(string $key)
    {
        return static::has($key)?$_SESSION[$key]:null;
    }




    public static function remove(string $key)
    {
        unset($_SESSION[$key]);
    }




    public static function all() : array
    {
        return $_SESSION;
    }




    public static function destroy() :void
    {
        foreach (static::all() as $key => $value){
            static::remove($key);
        }
    }




    public static function flash(string $key)
    {
        $value = static::get($key);
        static::remove($key);
        return $value;
    }





}