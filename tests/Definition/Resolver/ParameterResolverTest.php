<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DI\Definition\Resolver;

use Aether\Contracts\DI\Container as ContainerContract;
use Aether\DI\Container;
use Aether\DI\Definition\Exception\DependencyException;
use Aether\DI\Definition\Resolver\ParameterResolver;
use Aether\DI\Definition\Resolver\ParameterResolverInterface;
use Aether\Tests\DI\Fixtures\ClassWithDefaultValue;
use Aether\Tests\DI\Fixtures\ClassWithDependency;
use Aether\Tests\DI\Fixtures\ExtendedSampleClass;
use Aether\Tests\DI\Fixtures\SampleClass;
use Aether\Tests\DI\Fixtures\TypedClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(ParameterResolver::class)]
class ParameterResolverTest extends TestCase
{
    private MockObject|ContainerContract $container;
    private ParameterResolverInterface $resolver;

    public function setUp(): void
    {
        $this->container = $this->createMock(
            Container::class
        );

        $this->resolver = new ParameterResolver($this->container);
    }

    public function testReturnsEmptyArrayIfReflectionMethodIsNull(): void
    {
        self::assertSame([], $this->resolver->resolveParameters());
    }

    public function testThrowsExceptionIfParameterIsNotDefinedOrGuessable(): void
    {
        self::expectException(DependencyException::class);
        self::expectExceptionMessage('Parameter `$variable` has no value defined or guessable.');

        $reflection = new ReflectionClass(ClassWithDependency::class);

        $this->resolver->resolveParameters($reflection->getConstructor());
    }

    public function testGetParameterDefaultValue(): void
    {
        $reflection = new ReflectionClass(ClassWithDefaultValue::class);
        $parameters = $this->resolver->resolveParameters($reflection->getConstructor());

        self::assertSame(
            'default',
            $parameters[0]
        );
    }

    public function testGetParameterFromPassedParameters(): void
    {
        $reflection = new ReflectionClass(ExtendedSampleClass::class);
        $parameters = $this->resolver->resolveParameters(
            $reflection->getConstructor(),
            ['name' => 'john']
        );

        self::assertNotEmpty($parameters);
        self::assertCount(1, $parameters);
        self::assertSame('john', $parameters[0]);
    }

    public function testGetParameterFromContainer(): void
    {
        $this->container->expects(self::once())
            ->method('make')
            ->with(SampleClass::class, [])
            ->willReturn(new SampleClass());

        $reflection = new ReflectionClass(TypedClass::class);
        $parameters = $this->resolver->resolveParameters($reflection->getConstructor());

        self::assertCount(1, $parameters);
        self::assertInstanceOf(SampleClass::class, $parameters[0]);
    }
}
