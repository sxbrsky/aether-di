<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Definition\Binding;

use Closure;
use Sxbrsky\DependencyInjection\Definition\Definition;

final readonly class Factory implements Definition
{
    public Closure $value;
    public bool $shared;

    public function __construct(
        callable $callable,
        bool $shared = false
    ) {
        $this->value = $callable(...);
        $this->shared = $shared;
    }
}
