<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Tests\Benchmark;

use IonBytes\Bean\Container;
use IonBytes\Bean\ContainerInterface;

abstract class AbstractBenchCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected ContainerInterface $container;

    public function setup(): void {
        $this->container = new Container();
    }

    public function tearDown(): void {
        unset($this->container);
    }
}
