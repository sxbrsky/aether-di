<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Definition;

final class State
{
    /**
     * The bindings.
     *
     * @var array<string, \Sxbrsky\DependencyInjection\Definition\Definition> $bindings
     */
    public array $bindings = [];

    /**
     * The shared instances.
     *
     * @var array<string, int|float|string|callable|object> $instances
     */
    public array $instances = [];
}
