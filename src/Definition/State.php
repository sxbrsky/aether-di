<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Definition;

final class State
{
    /**
     * The bindings.
     *
     * @var array<string, \IonBytes\Bean\Definition\Definition> $bindings
     */
    public array $bindings = [];

    /**
     * The shared instances.
     *
     * @var array<string, int|float|string|callable|object> $instances
     */
    public array $instances = [];
}
