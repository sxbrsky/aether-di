<?php

/*
 * This file is part of the sxbrsky/dependency-injection.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection;

interface FactoryInterface
{
    /**
     * Initializes a new instance of requested class using binding and set of parameters.
     *
     * @param class-string<T>|string $abstract
     *  The unique identifier for the entry.
     * @param array<string, array|object|scalar|null> $parameters
     *  Parameters to construct a new class.
     *
     * @return ($abstract is class-string ? T : int|float|string|callable|object|null)
     *
     * @throws \Sxbrsky\DependencyInjection\Definition\Exception\CircularDependencyException
     * @throws \Sxbrsky\DependencyInjection\Exception\EntryNotFoundException
     *
     * @template T of object
     */
    public function make(string $abstract, array $parameters = []): int|float|string|callable|object|null;
}
