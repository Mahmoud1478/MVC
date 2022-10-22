<?php

namespace Src\Routing;

use Exception;

class Route
{

    public function __construct(private array $attributes)
    {
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->attributes['middleware'] ?? [];
    }

    /**
     * @param array $middleware
     */
    public function setMiddleware(array $middleware): void
    {
        $this->attributes['middleware'] = array_merge(
            $this->attributes['middleware'] ?? [],
            $middleware
        );
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->attributes['uri'] ?? null;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->attributes['uri'] = $uri;
    }

    /**
     * @return string|null
     */
    public function getPattern(): ?string
    {
        return $this->attributes['pattern'] ?? null;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->attributes['pattern'] = $pattern;
    }

    public function getParameters(): array
    {
        return $this->attributes['parametersPattern'];
    }


    /**
     * @return array|callable|string
     */
    public function getCallback(): callable|array|string
    {
        return $this->attributes['callback'];
    }

    /**
     * @param array|callable|string $callback
     */
    public function setCallback(callable|array|string $callback): void
    {
        $this->attributes['callback'] = $callback;
    }

    /**
     * @throws Exception
     */
    public function bindParameters(array $parameters): string
    {
        foreach ($this->attributes['parametersPattern'] as $key => $value) {
            if (!preg_match($value, $parameters[$key])) {
                throw new Exception("$parameters[$key] must match $value");
            }
        }
        return vsprintf(
            preg_replace('#{(.*?)}#', '%s', $this->attributes['uri']),
            array_values($parameters)
        );
    }

    public function generatePattern(): static
    {
        $this->attributes['pattern'] = '#^' . vsprintf($this->attributes['placeholder'],$this->attributes['parametersPattern']). '$#';
        return $this;
    }
    public function generateParametersPattern(): static
    {
        preg_match_all('#{(?<names>.\w*?(:.*?)?)}#', $this->attributes['uri'], $prams);
        $this->attributes['parametersPattern'] = [];
        foreach ($prams['names'] as $pram) {
            preg_match('/(?<name>.\w+):?(?<accept>\[(.*?)\])?/', $pram, $matches);
            $name = $matches['name'];
            $accept = $matches['accept'] ?? '.';
            $this->attributes['parametersPattern'][$name] = vsprintf('(%s+)', [$accept]);
        }
        return $this;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->attributes['namespace'] = $namespace;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->attributes['namespace'] ? $this->attributes['namespace'].'\\' : '';
    }

    /**
     * @return array
     */
    public function getParametersValues(): array
    {
        return $this->attributes['parametersValues'];
    }

    /**
     * @param array $parametersValues
     * @return Route
     */
    public function setParametersValues(array $parametersValues): static
    {
        $this->attributes['parametersValues'] = array_combine(
            array_keys($this->attributes['parametersPattern']),
            $parametersValues
        ) ;
        return $this;
    }


}