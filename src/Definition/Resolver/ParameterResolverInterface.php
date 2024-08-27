<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Definition\Resolver;

use ReflectionFunctionAbstract;

interface ParameterResolverInterface
{
    /**
     * Resolves a parameters for given method.
     *
     * @param null|\ReflectionFunctionAbstract $reflectionMethod
     *  The function for which we are solving parameters.
     * @param array<string, array|object|scalar|null> $parameters
     *  An additional array of parameters.
     *
     * @return array
     *  Returns array of resolved parameters.
     */
    public function resolveParameters(
        ReflectionFunctionAbstract $reflectionMethod = null,
        array $parameters = []
    ): array;
}
