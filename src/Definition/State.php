<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DI\Definition;

final class State
{
    /**
     * The bindings.
     *
     * @var array<string, \Aether\DI\Definition\Definition>
     */
    public array $bindings = [];

    /**
     * The shared instances.
     *
     * @var array<string, int|float|string|callable|object>
     */
    public array $instances = [];
}
