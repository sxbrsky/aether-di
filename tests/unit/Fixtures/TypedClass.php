<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Tests\Unit\Fixtures;

class TypedClass
{
    public function __construct(
        public SampleClass $sampleClass
    ) {
    }
}
