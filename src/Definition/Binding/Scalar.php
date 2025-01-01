<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DI\Definition\Binding;

use Aether\DI\Definition\Definition;

final readonly class Scalar implements Definition
{
    public function __construct(
        public int|float|string|bool $value
    ) {
    }
}
