<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Bean;

use Psr\Container\ContainerInterface as BaseContainerInterface;

interface ContainerInterface extends BaseContainerInterface, FactoryInterface, InvokerInterface
{
    /**
     * Returns an entry of the container by its id.
     *
     * @param string|class-string<T> $id
     *  The unique identifier for the entry.
     *
     * @return ($id is class-string ? T : int|float|string|callable|object)
     *  The retrieved service.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @template T of object
     */
    public function get(string $id): int|float|string|callable|object;

    /**
     * Registers an instance as shared in the container.
     *
     * @param string $id
     *  The unique identifier for the entry.
     * @param int|float|string|callable|object $instance
     *  The instance to register.
     *
     * @return int|float|string|callable|object
     *  Returns registered instance.
     */
    public function instance(string $id, int|float|string|callable|object $instance): int|float|string|callable|object;

    /**
     * Binds a given concrete with the container.
     *
     * @param class-string|string $abstract
     *  The alias.
     * @param int|float|string|callable|object $concrete
     *  The binding.
     * @param bool $shared
     *  Sets a shared binding.
     *
     * @return void
     */
    public function bind(string $abstract, int|float|string|callable|object $concrete, bool $shared = false): void;

    /**
     * Binds a given concrete with the container as shared instance.
     *
     * @param class-string|string $abstract
     *  The alias.
     * @param int|float|string|callable|object $concrete
     *  The binding.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function shared(string $abstract, int|float|string|callable|object $concrete): void;

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
