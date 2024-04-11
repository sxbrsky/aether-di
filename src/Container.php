<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container;

use Closure;
use Exception;
use IonBytes\Container\Exception\CircularDependencyException;
use IonBytes\Container\Exception\EntryNotFoundException;
use IonBytes\Container\Exception\ResolvingException;
use IonBytes\Container\Resolver\ParameterResolver;
use ReflectionClass;
use ReflectionException;

use function array_key_exists;
use function array_keys;
use function array_pop;
use function class_exists;
use function implode;
use function interface_exists;
use function is_string;
use function sprintf;

class Container implements ContainerInterface
{
    /** @var array{concrete: \Closure, shared: bool}[] $bindings */
    private array $bindings = [];

    /** @var array<string, bool> $buildStack */
    private array $buildStack = [];

    /** @var array<string, array|object|scalar|null> $instances */
    private array $instances = [];

    /**
     * @inheritDoc
     */
    public function has(string $id): bool {
        return array_key_exists($id, $this->instances) || array_key_exists($id, $this->bindings);
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): array|object|bool|float|int|string|null {
        try {
            return $this->make($id);
        } catch (Exception $e) {
            if ($this->has($id) || $e instanceof CircularDependencyException) {
                throw $e;
            }

            throw new EntryNotFoundException($id);
        }
    }

    /**
     * @inheritDoc
     */
    public function instance(string $id, object $instance): object {
        $this->instances[$id] = $instance;
        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Closure|string $concrete, array $parameters = []): array|object|bool|float|int|string|null {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        try {
            if (!(class_exists($concrete) || interface_exists($concrete))) {
                throw new ReflectionException("Class \"$concrete\" does not exist.");
            }

            $reflection = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new ResolvingException("The target [$concrete] is not exists.", 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new ResolvingException(
                sprintf(
                    'The target [%s] is not instantiable%s.',
                    $concrete,
                    !empty($this->buildStack)
                        ? ' while building [' . implode(',', array_keys($this->buildStack)) . ']'
                        : ''
                )
            );
        }

        if (isset($this->buildStack[$concrete])) {
            throw new CircularDependencyException(
                "Circular dependency detected while trying to resolve entry [$concrete]."
            );
        }

        $this->buildStack[$concrete] = true;

        try {
            $constructor = $reflection->getConstructor();
            if ($constructor !== null) {
                $resolver = new ParameterResolver($this);
                $instances = $resolver->resolveParameters($constructor, $parameters);

                return $reflection->newInstanceArgs($instances);
            }

            return $reflection->newInstanceWithoutConstructor();
        } finally {
            array_pop($this->buildStack);
        }
    }

    /**
     * @inheritDoc
     */
    public function bind(string $abstract, string|Closure|null $concrete, bool $shared = false): void {
        if (!$concrete instanceof Closure) {
            if ($concrete === null) {
                $concrete = $abstract;
            }

            $concrete = $this->getConcreteFactory($abstract, $concrete);
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared
        ];

        if ($shared) {
            $this->instances[$abstract] = $concrete;
        }
    }

    /**
     * @inheritDoc
     */
    public function shared(string $abstract, string|Closure|null $concrete): void {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @inheritDoc
     */
    public function isShared(string $abstract): bool {
        return isset($this->services[$abstract]) && $this->bindings[$abstract]['shared'] === true;
    }

    /**
     * @inheritDoc
     */
    public function make(Closure|string $abstract, array $parameters = []): array|object|bool|float|int|string|null {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        $instance = $abstract === $concrete || $concrete instanceof Closure
            ? $this->resolve($concrete)
            : $this->make($concrete, $parameters);

        if (is_string($abstract) && $this->isShared($abstract)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Gets the concrete.
     *
     * @param \Closure|string $abstract
     *  The alias.
     *
     * @return \Closure|string
     *  Returns concrete factory if found, otherwise returns $abstract.
     */
    protected function getConcrete(Closure|string $abstract): Closure|string {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Gets concrete factory.
     *
     * @param string $abstract
     *  The alias.
     * @param \Closure|class-string|string $concrete
     *  The binding.
     *
     * @return Closure(ContainerInterface, array=):(array|null|object|scalar)
     *  Returns the factory.
     */
    private function getConcreteFactory(string $abstract, string|Closure $concrete): Closure {
        return function (
            ContainerInterface $container,
            array              $parameters = []
        ) use (
            $abstract,
            $concrete
        ): array|object|bool|float|int|string|null {
            if ($abstract === $concrete) {
                return $container->resolve($concrete);
            }

            /** @var array<string, array|object|scalar|null> $parameters */
            return $container->make($concrete, $parameters);
        };
    }
}
