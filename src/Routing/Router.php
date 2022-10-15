<?php

namespace Src\Routing;


use Closure;
use ReflectionException;
use Src\Bootstrap\App;
use Src\Container\Exceptions\ContainerException;
use Src\Http\Request;
use Src\Http\Server;

class Router
{

    private static  RoutingProcessor $processor ;
    private static App $app;

    private function __construct(){}

    static function init(App $app): void
    {
        self::$app = $app;
        self::$processor = new RoutingProcessor();
    }

    public static function  group(array $attributes, Closure $callback): void
    {
        if (isset($attributes['middleware']) && is_string($attributes['middleware']))
        {
            $attributes['middleware'] = explode('|', $attributes['middleware']);
        }
        self::$processor->updateGroupStack($attributes)->then($callback)->popGroupStack();
    }


    public static function getGroupStack(): array
    {
        return self::$processor->getGroupStack();
    }

    private static function reqsType(string $uri): string
    {
        return (!strpos($uri, 'api')) ? 'web' : 'api';
    }

    public static function get(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'GET');
    }
    public static function post(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'POST');
    }
    public static function head(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'HEAD');
    }
    public static function put(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'PUT');
    }
    public static function patch(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'PATCH');
    }
    public static function delete(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri,$callback,'DELETE');
    }

    public static function namedRoutes(): array
    {
        return self::$processor->getnamedRoutes();
    }

    /**
     * @return mixed|null
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function resolve(): mixed
    {
        $request = self::$app->get(Request::class);
//        dd(trim($request->uri(),'/'));
        $route = self::$processor->match(trim($request->uri(),'/')??'/', $request->method());
        if (! $route) {
            echo '<h1 style="display: block;width: 100% ;text-align: center;font-weight: bold; text-transform:uppercase;padding: 100px 0px">404 not found</h1>';
            return 1;
        }
        return static::call($route);
    }


    public static function list(): array
    {
        return self::$processor->getRoutes();
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    private static function call(array $route)
    {
        $callback = $route['callback'];
        if (is_array($route['callback'])){
            [$class, $method] = $callback;
            return self::callbackFunction($class, $method, $route['prams']);
        }

//        if (is_callable($callback)) {
//            return call_user_func_array($callback, $args);
//        } elseif (is_string($callback)) {
//            list($callback, $method) = explode('@', $callback);
//            $controller = 'App\Controllers\\' . $callback;
//            return self::callbackFunction($controller, $method, array_merge($args));
//
//        } elseif (is_array($callback)) {
//            list($callback, $method) = $callback;
//            return self::callbackFunction($callback, $method, array_merge($args));
//        }

    }

    /**
     * @param $controller
     * @param $method
     * @param $args
     * @return mixed|void
     * @throws ReflectionException
     * @throws ContainerException
     */
    private static function callbackFunction($controller, $method, $args)
    {
        if (!class_exists($controller)){echo 'controller dose not exist ';}
        $controller = static::$app->get($controller);
        if (!method_exists($controller, $method)){echo 'method dose not exist ';}
        static::$app->methodResolve($controller , $method, $args);
        return static::$app->methodResolve($controller , $method, $args);
    }

    /**
     * @throws \Exception
     */
    public static function getByNameWithBinding(string $name , ?array $prams=[]): string
    {
        $route = self::$processor->getNamedRoutes()[$name];
        foreach ($route['pramsName'] as $key => $value){
            if (!preg_match($value,$prams[$key])){
                throw new \Exception("$prams[$key] must match $value");
            }
        }
        return Server::url(vsprintf($route['binding'],array_values($prams)));
    }

    private static function addRoute(string $uri,callable|string|array $callback , string $method): RoutingProcessor
    {
        $group =self::getGroupStack();
        $attr =end($group);
        $uri =  trim(($attr['prefix'] ?? '').'/' . trim($uri, '/'),'/');
        preg_match_all('/{(.*)}/',$uri , $prams);
        $pramsName = array_pop($prams);
        return static::$processor->setCurrentMethod($method)
            ->add('web',$method,array_merge([
                'uri' => $uri,
                'pattern' => '#^' . preg_replace('#{(.*?)}#', '(.*?)', $uri ).'$#',
                'callback' => $callback,
                'binding' => preg_replace('/{(.*)}/', '%s', $uri),
                'pramsName' =>array_combine($pramsName,array_fill(0,count($pramsName),'/.*/')),
                'named' => false,
        ],$attr))
            ->refreshIndex();
    }

    public static function getNamedRoutes(): array
    {
        return self::$processor->getNamedRoutes();
    }

}