<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Tests\Benchmark\Cases;

use IonBytes\Bean\Tests\Benchmark\AbstractBenchCase;
use IonBytes\Bean\Tests\Benchmark\Fixtures\FooBar;
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
        $this->container->make(FooBar::class);
    }
}
