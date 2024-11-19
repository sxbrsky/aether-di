<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DI\Fixtures;

class ExtendedSampleClass extends SampleClass
{
    public function __construct(
        public string $name
    ) {
    }
}
