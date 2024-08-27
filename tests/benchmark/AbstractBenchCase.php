<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Tests\Benchmark;

use Sxbrsky\DependencyInjection\Container;
use Sxbrsky\DependencyInjection\ContainerInterface;

abstract class AbstractBenchCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected ContainerInterface $container;

    public function setup(): void
    {
        $this->container = new Container();
    }

    public function tearDown(): void
    {
        unset($this->container);
    }
}
