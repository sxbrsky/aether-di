<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Definition\Binding;

use IonBytes\Container\Definition\Definition;

final readonly class Scalar implements Definition
{
    public function __construct(
        public int|float|string|bool $value
    ) {
    }
}
