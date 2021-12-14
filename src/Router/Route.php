<?php

namespace Src\Router;

use Src\Http\Request;

class Route
{
    private static array $routs = [
        'web'=>[
            'GET'=>[],
            'POST'=>[],
            'PUT'=>[],
            'DELETE'=>[],
            'PATCH'=>[]
        ],
        'api'=>[
            'GET'=>[],
            'POST'=>[],
            'PUT'=>[],
            'DELETE'=>[],
            'PATCH'=>[]
        ]

    ];
    private static ?string $middleware = null;
    private static ?string $prefix = null;

    private function __construct(){}

    private static function add(string $type,string $method,string $key ,array $value,)
    {
        static::$routs[$type][$method][$key]= $value;
    }
    private static function reqsType(string $uri): string
    {
        return (!strpos($uri,'api'))? 'web' :'api';
    }

    public static function get(string $uri ,callable|string|array $callback):void
    {
         $uri =  static::$prefix.'/'.trim($uri,'/');
         $type = static::reqsType($uri);
         $key = '#^'. preg_replace('/{(.*?)}/', '(.[a-z0-9-_]*?)',  $uri ). '$#';

         static::add($type,'GET',$key,['callback' => $callback,'middleware' => static::$middleware]);
    }

    public static function post(string $uri , callable|string|array $callback):void
    {
        $uri =  static::$prefix.'/'.trim($uri,'/');
        $type = static::reqsType($uri);
        $key = '#^'. preg_replace('/{(.*?)}/', '(.[a-z0-9-_]*?)',  $uri ). '$#';
        static::add($type,'POST',$key,['callback' => $callback,'middleware' => static::$middleware]);
    }
    public static function put(string $uri , callable|string|array $callback):void
    {

        $uri =  static::$prefix.'/'.trim($uri,'/');
        $type = static::reqsType($uri);
        $key = '#^'. preg_replace('/{(.*?)}/', '(.[a-z0-9-_]*?)',  $uri ). '$#';
        static::add($type,'PUT',$key,['callback' => $callback,'middleware' => static::$middleware]);
    }
    public static function patch(string $uri , callable|string|array $callback):void
    {
        $uri =  static::$prefix.'/'.trim($uri,'/');
        $type = static::reqsType($uri);
        $key = '#^'. preg_replace('/{(.*?)}/', '(.[a-z0-9-_]*?)',  $uri ). '$#';
        static::add($type,'PATCH',$key,['callback' => $callback,'middleware' => static::$middleware]);
    }
    public static function delete(string $uri , callable|string|array $callback):void
    {
        $uri =  static::$prefix.'/'.trim($uri,'/');
        $type = static::reqsType($uri);
        $key = '#^'. preg_replace('/{(.*?)}/', '(.[a-z0-9-_]*?)',  $uri ). '$#';
        static::add($type,'DELETE',$key,['callback' => $callback,'middleware' => static::$middleware]);
    }

    public static function prefix(string $prefix,callable $callback):void
    {
        $parent = static::$prefix;
        static::$prefix =$parent .'/'.trim($prefix,'/')?? '/' ;

        if(is_callable($callback)){
            call_user_func($callback);
        }else{

            throw \BadFunctionCallException('please provide valid callback');
        }
        static::$prefix = $parent;
    }
    public static function resolve()
    {
        $method = Request::method();
        $route= static::match(Request::url(),$method);
        if ($route){
           static::call($route['callback'],$route['prams'],$method);

            /*echo '<pre>';
            print_r($route);
            echo '</pre>';*/

        }else{
            echo '<h1 style="display: block;width: 100% ;text-align: center;font-weight: bold; text-transform:uppercase;padding: 100px 0px">404 not found</h1>';
        }
    }

    private static function match(string $uri , string $method): bool|array
    {
        $routes = static::$routs[static::reqsType($uri)][$method];
        foreach ($routes as $key => $value ) {
            if (preg_match($key, $uri, $matches)) {
                array_shift($matches);
                return array_merge($value,['prams'=> array_values($matches)]);
            }
        }
        return false;
    }
    public static function view(): array
    {
        return static::$routs;
    }

    private static function call(callable|string|array $callback,array $args,string $method):void
    {
        $method_args = !($method ==='GET')? Request::values():null;
        # var_dump(array_merge($args,['request'=>$method_args]));
        # var_dump(...array_merge($args,['request'=>$method_args]));
        echo $method.'<br>';
        if (is_callable($callback)){
            call_user_func_array($callback,$args);
        }elseif (is_string($callback)){
           list($callback,$method) = explode('@',$callback);
           $controller =  'App\Controllers\\'.$callback;
          static::callbackFunction($controller,$method,array_merge($args,['request'=>$method_args]));

        } elseif (is_array($callback)){
            list($callback,$method) = $callback;
            static::callbackFunction($callback,$method,array_merge($args,['request'=>$method_args]));
        }

    }
    private static function callbackFunction($controller , $method , $args):void
    {
        if (class_exists($controller)){
            $obj = new $controller;
            if (method_exists($obj,$method)){
                call_user_func_array( [$obj,$method],$args);
            }else{
                echo 'method dose not exist ';
            }

        }else{
            echo 'controller dose not exist ';
        }
    }
}