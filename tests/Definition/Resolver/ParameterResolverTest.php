<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DependencyInjection\Definition\Resolver;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Aether\DependencyInjection\Container;
use Aether\DependencyInjection\ContainerInterface;
use Aether\DependencyInjection\Definition\Exception\DependencyException;
use Aether\DependencyInjection\Definition\Resolver\ParameterResolver;
use Aether\DependencyInjection\Definition\Resolver\ParameterResolverInterface;
use Aether\Tests\DependencyInjection\Fixtures\ClassWithDefaultValue;
use Aether\Tests\DependencyInjection\Fixtures\ClassWithDependency;
use Aether\Tests\DependencyInjection\Fixtures\ExtendedSampleClass;
use Aether\Tests\DependencyInjection\Fixtures\SampleClass;
use Aether\Tests\DependencyInjection\Fixtures\TypedClass;

#[CoversClass(ParameterResolver::class)]
class ParameterResolverTest extends TestCase
{
    private MockObject|ContainerInterface $container;
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
