<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Tests\Unit\Resolver;

use IonBytes\Container\Container;
use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Exception\DependencyException;
use IonBytes\Container\Resolver\ParameterResolver;
use IonBytes\Container\Tests\Unit\Fixtures\ClassWithDefaultValue;
use IonBytes\Container\Tests\Unit\Fixtures\ClassWithDependency;
use IonBytes\Container\Tests\Unit\Fixtures\ExtendedSampleClass;
use IonBytes\Container\Tests\Unit\Fixtures\SampleClass;
use IonBytes\Container\Tests\Unit\Fixtures\TypedClass;
use IonBytes\Container\Tests\Unit\Fixtures\UntypedClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(ParameterResolver::class)]
#[CoversClass(Container::class)]
class ParameterResolverTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private ContainerInterface $container;

    public function setUp(): void {
        $this->container = new Container();
    }

    public function testReturnsEmptyArrayIfReflectionMethodIsNull(): void {
        $resolver = new ParameterResolver($this->container);
        self::assertSame([], $resolver->resolveParameters());
    }

    public function testThrowsExceptionIfParameterIsNotDefinedOrGuessable(): void {
        self::expectException(DependencyException::class);
        self::expectExceptionMessage('Parameter `$variable` has no value defined or guessable.');

        $reflection = new ReflectionClass(ClassWithDependency::class);

        $resolver = new ParameterResolver($this->container);
        $resolver->resolveParameters($reflection->getConstructor());
    }

    public function testGetParameterDefaultValue(): void {
        $reflection = new ReflectionClass(ClassWithDefaultValue::class);

        $resolver = new ParameterResolver($this->container);
        $parameters = $resolver->resolveParameters($reflection->getConstructor());

        self::assertSame(
            'default',
            $parameters[0]
        );
    }

    public function testGetParameterFromPassedParameters(): void {
        $reflection = new ReflectionClass(ExtendedSampleClass::class);

        $resolver = new ParameterResolver($this->container);
        $parameters = $resolver->resolveParameters(
            $reflection->getConstructor(),
            ['name' => 'john']
        );

        self::assertNotEmpty($parameters);
        self::assertCount(1, $parameters);
        self::assertSame('john', $parameters[0]);
    }

    public function testGetParameterFromContainer(): void {
        $this->container->bind(SampleClass::class, SampleClass::class);
        $reflection = new ReflectionClass(TypedClass::class);

        $resolver = new ParameterResolver($this->container);
        $parameters = $resolver->resolveParameters($reflection->getConstructor());

        self::assertCount(1, $parameters);
        self::assertInstanceOf(SampleClass::class, $parameters[0]);
    }
}
