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
     * @return ($abstract is class-string ? T : int|float|string|callable|object)
     *
     * @throws \IonBytes\Container\Definition\Exception\CircularDependencyException
     * @throws \IonBytes\Container\Exception\EntryNotFoundException
     *
     * @template T of object
     */
    public function make(string $abstract, array $parameters = []): int|float|string|callable|object;
}
