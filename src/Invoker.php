<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DI;

use Aether\Contracts\DI\Container;
use Aether\Contracts\DI\Exception\ContainerException;
use Aether\Contracts\DI\Exception\RuntimeException;
use Aether\Contracts\DI\Invoker as InvokerContract;
use Aether\DI\Definition\Resolver\ParameterResolver;
use Aether\DI\Definition\Resolver\ParameterResolverInterface;
use Closure;

use function is_array;
use function is_callable;
use function is_string;

use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

final class Invoker implements InvokerContract
{
    private readonly ParameterResolverInterface $resolver;

    public function __construct(
        private readonly Container $container
    ) {
        $this->resolver = new ParameterResolver($container);
    }

    /**
     * @inheritDoc
     */
    public function call(callable|array|string $callable, array $parameters = []): mixed
    {
        if (is_array($callable)) {
            if (! isset($callable[1])) {
                $callable[1] = '__invoke';
            }

            [$class, $method] = $callable;

            try {
                if (is_string($class)) {
                    $class = $this->container->get($class);
                }

                $reflection = new ReflectionMethod($class, $method);
            } catch (ReflectionException $e) {
                throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            }

            return $reflection->invokeArgs($class, $this->resolver->resolveParameters($reflection, $parameters));
        }

        if (is_string($callable) && is_callable($callable)) {
            $callable = $callable(...);
        }

        if ($callable instanceof Closure) {
            try {
                $reflection = new ReflectionFunction($callable);
            } catch (ReflectionException $e) {
                throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            }

            return $reflection->invokeArgs($this->resolver->resolveParameters($reflection, $parameters));
        }

        throw new RuntimeException("Can't resolve given callable.");
    }
}
