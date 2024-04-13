<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Definition\Resolver;

use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Definition\Binding\Alias;
use IonBytes\Container\Definition\Binding\Factory as FactoryBinding;
use IonBytes\Container\Definition\Exception\CircularDependencyException;
use IonBytes\Container\Definition\State;
use IonBytes\Container\Exception\ContainerException;
use IonBytes\Container\Exception\EntryNotFoundException;
use IonBytes\Container\FactoryInterface;
use ReflectionClass;

use function array_key_exists;
use function class_exists;
use function interface_exists;

final class DefinitionResolver implements FactoryInterface
{
    /**
     * The stack of concrete currently being built.
     *
     * @var array<string, bool> $buildStack
     */
    private array $buildStack = [];

    /**
     * The parameter resolver.
     *
     * @var \IonBytes\Container\Definition\Resolver\ParameterResolverInterface $parameterResolver
     */
    private ParameterResolverInterface $parameterResolver;

    public function __construct(
        private readonly State              $state,
        private readonly ContainerInterface $container
    ) {
        $this->parameterResolver = new ParameterResolver($this->container);
    }

    /**
     * @inheritDoc
     */
    public function make(string $abstract, array $parameters = []): int|float|string|callable|object {
        if (array_key_exists($abstract, $this->state->instances)) {
            return $this->state->instances[$abstract];
        }

        $concrete = $this->state->bindings[$abstract] ?? null;
        if ($concrete === null) {
            return $this->autowire($abstract, $parameters);
        }

        if ($concrete instanceof Alias) {
            $instance = $abstract === $concrete->value
                ? $this->autowire($concrete->value, $parameters)
                : $this->make($concrete->value, $parameters);

            if ($concrete->shared === true) {
                $this->state->instances[$abstract] = $instance;
            }

            return $instance;
        }

        if ($concrete instanceof FactoryBinding) {
            return ($concrete->value)($this->container);
        }

        return $concrete->value;
    }

    /**
     * Automatically create class.
     *
     * @param string $abstract
     *  The abstract.
     * @param array<string, array|object|scalar|null> $parameters
     *  Parameters to construct a new class.
     *
     * @return object
     *  Return initialized class.
     *
     * @throws \ReflectionException
     */
    private function autowire(string $abstract, array $parameters = []): object {
        if (isset($this->buildStack[$abstract])) {
            throw new CircularDependencyException(
                "Circular dependency detected while trying to resolve entry $abstract."
            );
        }

        if (!(class_exists($abstract) || interface_exists($abstract))) {
            throw new EntryNotFoundException($abstract);
        }

        $reflection = new ReflectionClass($abstract);

        if (!$reflection->isInstantiable()) {
            throw new ContainerException(
                "$abstract is not instantiable."
            );
        }

        $this->buildStack[$abstract] = true;

        try {
            $constructor = $reflection->getConstructor();

            if ($constructor !== null) {
                return $reflection->newInstanceArgs(
                    $this->parameterResolver->resolveParameters($constructor, $parameters)
                );
            }

            return $reflection->newInstanceWithoutConstructor();
        } finally {
            unset($this->buildStack[$abstract]);
        }
    }
}
