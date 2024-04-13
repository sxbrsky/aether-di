<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Definition\Resolver;

use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Definition\Exception\DependencyException;
use ReflectionFunctionAbstract;

use function array_key_exists;

final class ParameterResolver implements ParameterResolverInterface
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveParameters(
        ReflectionFunctionAbstract $reflectionMethod = null,
        array                      $parameters = []
    ): array {
        if ($reflectionMethod === null) {
            return [];
        }

        $arguments = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            /** @var ?\ReflectionNamedType $type */
            $type = $parameter->getType();

            if (array_key_exists($parameter->getName(), $parameters)) {
                $arguments[] = $parameters[$parameter->getName()];
            } elseif ($type !== null && !$type->isBuiltin()) {
                $arguments[] = $this->container->make($type->getName(), $parameters);
            } else {
                if ($parameter->isDefaultValueAvailable() || $parameter->isOptional()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new DependencyException(
                    "Parameter `\${$parameter->getName()}` has no value defined or guessable."
                );
            }
        }

        return $arguments;
    }
}
