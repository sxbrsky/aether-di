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
use Psr\Container\ContainerInterface as BaseContainerInterface;

interface ContainerInterface extends BaseContainerInterface, FactoryInterface
{
    /**
     * Returns an entry of the container by its id.
     *
     * @param string|class-string<T> $id
     *  The unique identifier for the entry.
     *
     * @return ($id is class-string ? T : array|object|scalar|null)
     *  The retrieved service.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @template T of object
     */
    public function get(string $id): array|object|bool|float|int|string|null;

    /**
     * Registers an instance as shared in the container.
     *
     * @param string $id
     *  The unique identifier for the entry.
     * @param object $instance
     *  The instance to register.
     *
     * @return object
     *  Returns registered instance.
     */
    public function instance(string $id, object $instance): object;

    /**
     * Resolves given concrete.
     *
     * @param \Closure|string $concrete
     *  The concrete service to resolve.
     * @param array<string, array|object|scalar|null> $parameters
     *   Parameters to construct a new class.
     *
     * @return array|object|scalar|null
     */
    public function resolve(Closure|string $concrete, array $parameters = []): array|object|bool|float|int|string|null;

    /**
     * Binds a given concrete with the container.
     *
     * @param class-string|string $abstract
     *  The alias.
     * @param \Closure|null|string $concrete
     *  The binding.
     * @param bool $shared
     *  Sets a shared binding.
     *
     * @return void
     */
    public function bind(string $abstract, Closure|null|string $concrete, bool $shared = false): void;

    /**
     * Binds a given concrete with the container as shared instance.
     *
     * @param class-string|string $abstract
     *  The alias.
     * @param \Closure|null|string $concrete
     *  The binding.
     *
     * @return void
     */
    public function shared(string $abstract, Closure|null|string $concrete): void;

    /**
     * Checks if a given concrete is shared.
     *
     * @param string $abstract
     *  The alias.
     *
     * @return bool
     *  Returns true if the given concrete is shared, otherwise false.
     */
    public function isShared(string $abstract): bool;
}
