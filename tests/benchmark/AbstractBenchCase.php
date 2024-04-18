<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Bean\Tests\Benchmark;

use Bean\Container;
use Bean\ContainerInterface;

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
