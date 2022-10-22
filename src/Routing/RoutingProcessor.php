<?php

namespace Src\Routing;

use ArrayAccess;

class RoutingProcessor
{
    /**
     * @var array<Route> $routes
     */
    private array $routes;
    private string $currentMethod = 'GET';
    private array $groupStack = [];
    private array $namedRoutes = [];
    private int $index = 0;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @return array
     */
    public function getGroupStack(): array
    {
        return $this->groupStack;
    }

    /**
     * @return string
     */
    public function getCurrentMethod(): string
    {
        return $this->currentMethod;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getNamedRoutes(): array
    {
        return $this->namedRoutes;
    }

    /**
     * @param string $currentMethod
     */
    public function setCurrentMethod(string $currentMethod): static
    {
        $this->currentMethod = $currentMethod;
//        $this->index = count($this->routes[$currentMethod]??[]) 1;
        return $this;
    }

    public function updateGroupStack(array $attributes): static
    {
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }
        $this->groupStack[] = $attributes;
        return $this;
    }

    public function then(callable $callback): static
    {
        $callback($this);
        return $this;
    }

    public function popGroupStack(): static
    {
        array_pop($this->groupStack);
        return $this;
    }

    protected function mergeWithLastGroup($new): array
    {
        return $this->mergeGroup($new, end($this->groupStack));
    }

    public function mergeGroup($new, $old): array
    {

        $new['prefix'] = static::formatGroupPrefix($new, $old);

        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        if (isset($old['as'])) {
            $new['as'] = $old['as'] . (isset($new['as']) ? '.' . $new['as'] : '');
        }

        if (isset($old['suffix']) && !isset($new['suffix'])) {
            $new['suffix'] = $old['suffix'];
        }
        if (isset($new['namespace'])) {
            $new['namespace'] = ($old['namespace'] ?? '') . $new['namespace'];
        }
        return array_merge_recursive(static::except($old, ['namespace', 'prefix', 'as', 'suffix']), $new);
    }

    public static function formatGroupPrefix($new, $old)
    {
        $oldPrefix = $old['prefix'] ?? null;

        if (isset($new['prefix'])) {
            return trim($oldPrefix ?? '', '/') . '/' . trim($new['prefix'], '/');
        }

        return $oldPrefix;
    }

    public static function except($array, $keys)
    {
        static::forget($array, $keys);

        return $array;
    }

    public static function forget(&$array, $keys): void
    {
        $original = &$array;

        $keys = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    public function name(string $name): static
    {
//        $route = &$this->routes[$this->currentMethod][$this->index];
//        $this->namedRoutes[$this->createNameForRoute($name)] = [
//            'uri' => $route->getUri(),
//            'pattern' => $route,
//            'placeholder' => $route['placeholder'],
//            'parameters' => $route['parameters']
//        ];
        $this->namedRoutes[$this->createNameForRoute($name)] = [
            'index' => $this->index,
            'method' => $this->currentMethod
        ];
        return $this;
    }

    private function createNameForRoute(string $name): string
    {
        if (isset(end($this->groupStack)['as'])) {
            return end($this->groupStack)['as'] . '.' . $name;
        }
        return $name;
    }

    public function prefix(string $prefix): static
    {
        return $this;
    }

    public function middleware(array|string $middlewares): static
    {
        $this->routes[$this->currentMethod][$this->index]->setMiddleware(
                is_string($middlewares) ? explode('|', $middlewares) : $middlewares
        );
        return $this;
    }

    public function match(string $uri, string $method): ?Route
    {
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route->getPattern(), $uri, $matches)) {
                array_shift($matches);
                return $route->setParametersValues($matches);
            }
        }
        return null;
    }

    public function routeExists(string $uri, string $method): bool
    {
        return false;
    }

    public function add(string $type, string $method, Route $route): static
    {
        $this->routes[$method][] = $route;
        return $this;
    }

    public function increaseIndex(): static
    {
        $this->index = count($this->routes[$this->currentMethod])-1;
        return $this;
    }

    public function routes(string $method,?int $index = null) :array|Route
    {
        if ($index){
            return $this->routes[$method][$index];
        }
        return $this->routes[$method];
    }
}