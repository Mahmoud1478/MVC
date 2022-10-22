<?php

namespace Src\Routing;


use Closure;
use Exception;
use ReflectionException;
use Src\Bootstrap\App;
use Src\Container\Exceptions\ContainerException;
use Src\Http\Request;
use Src\Http\Server;

class Router
{

    private static RoutingProcessor $processor;

    function __construct(
        public readonly App $app,
    )
    {
        self::$processor = new RoutingProcessor();
    }

    public function group(array $attributes, Closure $callback): void
    {
        if (isset($attributes['middleware']) && is_string($attributes['middleware'])) {
            $attributes['middleware'] = explode('|', $attributes['middleware']);
        }
        self::$processor->updateGroupStack($attributes);
        $callback($this);
        static::$processor->popGroupStack();
    }


    public static function getGroupStack(): array
    {
        return self::$processor->getGroupStack();
    }

    private static function reqsType(string $uri): string
    {
        return (!strpos($uri, 'api')) ? 'web' : 'api';
    }

    public function get(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'GET');
    }

    public function post(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'POST');
    }

    public function head(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'HEAD');
    }

    public function put(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'PUT');
    }

    public function patch(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'PATCH');
    }

    public function delete(string $uri, callable|string|array $callback): RoutingProcessor
    {
        return static::addRoute($uri, $callback, 'DELETE');
    }

    public static function namedRoutes(): array
    {
        return self::$processor->getnamedRoutes();
    }

    /**
     * @return mixed|null
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Exception
     */
    public function resolve(): mixed
    {
        $request = $this->app->get(Request::class);
        $route = self::$processor->match(rtrim($request->uri(), '/') ?? '/', $request->method());
        if ($route) {
            return $this->call($route);
        }
        throw new Exception('Route " '.$request->uri().' " Not Fount');
    }


    public function list(): array
    {
        return self::$processor->getRoutes();
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Exception
     */
    private function call(Route $route)
    {
        $callback = $route->getCallback();
        if (is_callable($callback)){
            return $this->app->methodResolve(null,$callback, $route->getParametersValues());
        }
        if (is_array($callback)) {
            [$controller, $method] = $callback;
        }
        else{
            [$controller, $method] = explode('@',$callback);
            $controller = $route->getNamespace().$controller ;
        }
        return $this->callFromClass($controller,$method,$route->getParametersValues());

    }

    /**
     * @param $controller
     * @param $method
     * @param $args
     * @return mixed|void
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Exception
     */
    private function callFromClass($controller, $method, $args)
    {
        if (!class_exists($controller)) {
            throw new Exception('Class "'.$controller.'" dose not exist ');
        }
        $controller = $this->app->get($controller);
        if (!method_exists($controller, $method)) {
            throw new Exception('method "'.$method.'" dose not exist ');
        }
        return $this->app->methodResolve($controller, $method, $args);
    }

    /**
     * @throws Exception
     */
    public static function getByNameWithBinding(string $name, ?array $prams = []): string
    {
        $index = self::$processor->getNamedRoutes()[$name];
        $route = self::$processor->routes($index['method'],$index['index']);
        return Server::url($route->bindParameters($prams));
    }

    private static function addRoute(string $uri, callable|string|array $callback, string $method): RoutingProcessor
    {
        $group = self::getGroupStack();
        $attr = end($group);
        $uri = rtrim(($attr['prefix'] ?? '') . '/' . trim($uri, '/'), '/');
        $route = (new Route(array_merge([
            'uri' => $uri,
            'callback' => $callback,
            'placeholder' => preg_replace('#{(.*?)}#', '%s', $uri),
        ], $attr)))->generateParametersPattern()->generatePattern();

        return static::$processor->setCurrentMethod($method)->add('web', $method, $route)->increaseIndex();
    }

    public  function getNamedRoutes(): array
    {
        return self::$processor->getNamedRoutes();
    }

}