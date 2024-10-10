<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DependencyInjection\Definition\Binding;

use Aether\DependencyInjection\Definition\Definition;

final readonly class Scalar implements Definition
{
    public function __construct(
        public int|float|string|bool $value
    ) {
    }
}
