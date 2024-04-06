<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Tests\Benchmark;

use IonBytes\Container\Container;
use IonBytes\Container\ContainerInterface;

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
