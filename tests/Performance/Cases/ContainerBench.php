<?php

/*
 * This file is part of the sxbrsky/dependency-injection.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\Performance\Cases;

use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\Assert;
use PhpBench\Attributes\BeforeMethods;
use Sxbrsky\Performance\AbstractBenchCase;
use Sxbrsky\Performance\Fixtures\FooBar;

class ContainerBench extends AbstractBenchCase
{
    #[BeforeMethods('setup')]
    #[AfterMethods('tearDown')]
    #[Assert('mode(variant.time.avg) < 5ms')]
    #[Assert('mode(variant.mem.real) < 10mb')]
    public function benchResolvePerformance(): void
    {
        $this->container->make(FooBar::class);
    }
}
