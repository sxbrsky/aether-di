<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Tests\Benchmark\Cases;

use IonBytes\Container\Tests\Benchmark\AbstractBenchCase;
use IonBytes\Container\Tests\Benchmark\Fixtures\FooBar;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\Assert;
use PhpBench\Attributes\BeforeMethods;

class ContainerBench extends AbstractBenchCase
{
    #[BeforeMethods('setup')]
    #[AfterMethods('tearDown')]
    #[Assert('mode(variant.time.avg) < 5ms')]
    #[Assert('mode(variant.mem.real) < 10mb')]
    public function benchResolvePerformance(): void {
        $this->container->resolve(FooBar::class);
    }
}
