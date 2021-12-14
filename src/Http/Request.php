<?php

namespace Src\Http;

class Request
{
    private static string $base_url ;

    private static string $url;

    private static  string$full_url;

    private static string $query_string;

    private static string $script_name;

    private function __construct(){}

    public static function handle()
    {
        static::$script_name = trim(dirname(Server::get('SCRIPT_NAME')),'\\');
        static::setBaseUrl();
        static::setUrl();
    }


    private static function setBaseUrl(): void
    {
         static::$base_url = Server::get('REQUEST_SCHEME').'://'.Server::get('HTTP_HOST').static::$script_name;
    }

    public static function baseUrl(): string
    {
        return static::$base_url;
    }


    private static function setUrl(): void
    {
        $request_uri = rtrim(str_replace(static::$script_name,'',urldecode(Server::get('REQUEST_URI'))),'/');
        static::$full_url = $request_uri;
        $request_query_string = '';
        if (str_contains($request_uri, '?')){
            list($request_uri ,$request_query_string) = explode('?',$request_uri);
        }
        static::$query_string = $request_query_string;
        static::$url = $request_uri;
    }

    private static function value(string $key,array $type=null)
    {
        $type = $type ?? $_REQUEST;
        return static::has($type,$key)?$type[$key]:null;
    }

    public static function url(): string
    {
        return  static::$url ? static::$url :'/';
    }

    public static function queryString(): string
    {
        return static::$query_string ;
    }


    public static function fullUrl(): string
    {
        return static::$full_url;
    }

    public static function method() :string
    {
        return Server::get('REQUEST_METHOD');
    }


    public static function has(array $type,string $key): bool
    {
        return array_key_exists($key,$type);
    }

    public static function get(string $key)
    {
        return static::value($key,$_GET);
    }

    public static function post(string $key)
    {
        return static::value($key,$_POST);
    }

    public static function values():array
    {
        return $_REQUEST;
    }
}
