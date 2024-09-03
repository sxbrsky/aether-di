<?php

/*
 * This file is part of the sxbrsky/dependency-injection.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Invoker;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Sxbrsky\DependencyInjection\ContainerInterface;
use Sxbrsky\DependencyInjection\Definition\Resolver\ParameterResolver;
use Sxbrsky\DependencyInjection\Definition\Resolver\ParameterResolverInterface;
use Sxbrsky\DependencyInjection\Exception\ContainerException;
use Sxbrsky\DependencyInjection\Exception\RuntimeException;
use Sxbrsky\DependencyInjection\InvokerInterface;

use function is_array;
use function is_callable;
use function is_string;

final class Invoker implements InvokerInterface
{
    private readonly ParameterResolverInterface $resolver;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
        $this->resolver = new ParameterResolver($container);
    }

    /**
     * @inheritDoc
     */
    public function call(callable|array|string $callable, array $parameters = []): mixed
    {
        if (is_array($callable)) {
            if (!isset($callable[1])) {
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
