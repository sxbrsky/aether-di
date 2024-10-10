<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DependencyInjection\Definition;

final class State
{
    /**
     * The bindings.
     *
     * @var array<string, \Aether\DependencyInjection\Definition\Definition> $bindings
     */
    public array $bindings = [];

    /**
     * The shared instances.
     *
     * @var array<string, int|float|string|callable|object> $instances
     */
    public array $instances = [];
}
