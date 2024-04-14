<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container;

interface InvokerInterface
{
    /**
     * Calls the given function using the given parameters. Missing parameters will be resolved from the container.
     *
     * @param callable|non-empty-string|array{class-string, non-empty-string} $callable
     *  string - Class string or function name to execute.
     *  array - First element is the class, and the second method if not set then sets the default method __invoke.
     * @param array<string, array|object|scalar|null> $parameters
     *  Parameters to use.
     *
     * @return mixed
     *  Result of the function.
     */
    public function call(callable|string|array $callable, array $parameters = []): mixed;
}
