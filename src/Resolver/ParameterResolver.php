<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Resolver;

use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Exception\DependencyException;
use ReflectionFunctionAbstract;

use function array_key_exists;

final class ParameterResolver implements ParameterResolverInterface
{
    private array $with = [];

    public function __construct(
        private ContainerInterface $container
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveParameters(
        ReflectionFunctionAbstract $reflectionMethod = null,
        array $parameters = []
    ): array {
        if ($reflectionMethod === null) {
            return [];
        }

        foreach ($reflectionMethod->getParameters() as $parameter) {
            /** @var ?\ReflectionNamedType $type */
            $type = $parameter->getType();

            if (array_key_exists($parameter->getName(), $parameters)) {
                $this->with[] = $parameters[$parameter->getName()];
            } elseif ($type !== null && !$type->isBuiltin()) {
                $this->with[] = $this->container->make($type->getName(), $parameters);
            } else {
                if ($parameter->isDefaultValueAvailable() || $parameter->isOptional()) {
                    $this->with[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new DependencyException(
                    "Parameter `\${$parameter->getName()}` has no value defined or guessable."
                );
            }
        }

        return $this->with;
    }
}
