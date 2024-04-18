<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Bean\Tests\Unit\Fixtures;

class ClassBCircularDependency
{
    public function __construct(
        public ClassACircularDependency $class
    ) {
    }
}
