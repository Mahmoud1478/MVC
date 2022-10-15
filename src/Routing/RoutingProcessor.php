<?php

namespace Src\Routing;

use ArrayAccess;

class RoutingProcessor
{
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
        $route = &$this->routes[$this->currentMethod][$this->index];
        $this->namedRoutes[$this->createNameForRoute($name)] = [
            'uri' => $route['uri'],
            'pattern' => $route['pattern'],
            'binding' => $route['binding'],
            'pramsName' => $route['pramsName']
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
        $this->routes[$this->currentMethod][$this->index]['middleware'] = array_merge(
            $this->routes[$this->currentMethod][$this->index]['middleware']??[],
                is_string($middlewares) ? explode('|', $middlewares) : $middlewares
        );
        return $this;
    }

    public function whereDigit(string|array $pram): static
    {
        return $this;
    }

    public function whereSting(string|array $pram): static
    {
        return $this;
    }

    public function whereDigitExcept(string|array $pram, ?array $except = null): static
    {
        return $this;
    }

    public function whereStingExcept(string|array $pram, ?array $except = null): static
    {
        return $this;
    }

    public function whereDigitAnd(string|array $pram, ?array $and = null): static
    {
        return $this;
    }

    public function whereStingAnd(string|array $pram, ?array $and = null): static
    {
        return $this;
    }

    public function match(string $uri, string $method): bool|array
    {
        foreach ($this->routes[$method] as $value) {
            $fullyMatched = true;
            if (preg_match($value['pattern'], $uri, $matches)) {
                array_shift($matches);
                foreach ($matches as $pram){
                    if (strpos($pram,'/')){
                        $fullyMatched = false;
                        break;
                    }
                }
                if ($fullyMatched){
                    return array_merge($value, [
                        'prams' => array_combine(array_keys($value['pramsName']),$matches),
                    ]);
                }

            }
        }
        return false;
    }

    public function routeExists(string $uri, string $method): bool
    {
        return false;
    }

    public function add(string $type, string $method, array $value): static
    {
        $this->routes[$method][] = $value;
        return $this;
    }

    public function refreshIndex(): static
    {
        $this->index = count($this->routes[$this->currentMethod]) - 1;
        return $this;
    }
}