<?php

namespace Src\Container;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Src\Container\Exceptions\ContainerException;

abstract class ServiceContainer implements ContainerInterface
{
    private array $entries = [];

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            $class = $this->entries[$id];
            if (is_callable($class)) {
                return $class($this);
            }
            $id = $class;
        }
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function bind(string $id, callable|string $concrete): static
    {
        $this->entries[$id] = $concrete;
        return $this;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function resolve(string $id)
    {
        // 1- inspect the class ;
        $reflection = new ReflectionClass($id);
        if (!$reflection->isInstantiable()) {
            throw new ContainerException('Class "' . $id . '" is not instantiable !');
        }
        // 2- inspect the constructor
        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return new $id;
        }
        // 3- inspect constructor prams;
        $prams = $constructor->getParameters();
        if (!$prams) {
            return new $id;
        }
        // 4- tye to get prams;
        $dependencies = array_map(function (ReflectionParameter $pram) use ($id) {
            $type = $pram->getType();
            $name = $pram->getName();
            if (!$type) {
                throw new ContainerException('Failed to resolve class "' . $id . '" because pram ' . $name . ' is missing type');
            }
            if ($type instanceof ReflectionUnionType) {
                throw new ContainerException('Failed to resolve class "' . $id . '" because pram ' . $name . ' is union type');
            }
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                return $this->get($type->getName());
            }
            throw new ContainerException('Failed to resolve class "' . $id . '" because pram ' . $name . ' is builtin type');
        }, $prams);
        // 5- return the class;
        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * @throws ReflectionException|ContainerException
     */
    public function methodResolve($object, string|Closure $methodName, array $args)
    {
        if (is_null($object)){
            $reflection = new ReflectionFunction($methodName);

        }else {
            $reflection = new ReflectionMethod($object::class, $methodName);
        }
        $prams = $reflection->getParameters();
        if (!$prams) {
            $reflection->invoke($object);
        }
        $finalArgs = [];
        foreach ($prams as $pram) {
            $type = $pram->getType();
            $name = $pram->getName();
            if (isset($type) && $type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $value = $this->get($type->getName());
            } elseif ($pram->isOptional()) {
                $value = $args[$name] ?? $pram->getDefaultValue();
            } else {
                $value = $args[$name] ?? null;
            }
            $finalArgs[$name] = $value;
        }
        if ($object){
            return $reflection->invokeArgs($object, array_values($finalArgs));
        }
        return $reflection->invokeArgs(array_values($finalArgs));
    }

}