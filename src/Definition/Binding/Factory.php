<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Definition\Binding;

use Closure;
use IonBytes\Bean\Definition\Definition;

final readonly class Factory implements Definition
{
    public Closure $value;
    public bool $shared;

    public function __construct(
        callable $callable,
        bool     $shared = false
    ) {
        $this->value = $callable(...);
        $this->shared = $shared;
    }
}
