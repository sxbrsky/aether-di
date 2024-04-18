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

use IonBytes\Bean\Definition\Definition;

final readonly class Alias implements Definition
{
    public function __construct(
        public string $value,
        public bool   $shared = false
    ) {
    }
}
