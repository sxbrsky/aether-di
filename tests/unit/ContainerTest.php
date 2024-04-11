<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Tests\Unit;

use IonBytes\Container\Container;
use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Exception\CircularDependencyException;
use IonBytes\Container\Exception\ResolvingException;
use IonBytes\Container\Resolver\ParameterResolver;
use IonBytes\Container\Tests\Unit\Fixtures\ClassACircularDependency;
use IonBytes\Container\Tests\Unit\Fixtures\SampleClass;
use IonBytes\Container\Tests\Unit\Fixtures\SampleInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Container::class)]
#[CoversClass(ParameterResolver::class)]
class ContainerTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private ContainerInterface $container;

    public function setUp(): void {
        $this->container = new Container();
    }

    public function testContainerKnowTheEntry(): void {
        $this->container->instance(SampleClass::class, new SampleClass());

        self::assertTrue(
            $this->container->has(SampleClass::class)
        );
    }

    public function testContainerDoesNotKnowTheEntry(): void {
        self::assertFalse(
            $this->container->has('foo')
        );
    }

    public function testSharedInstanceReturnsTheInstance(): void {
        $instance = new SampleClass();

        self::assertSame(
            $instance,
            $this->container->instance(SampleClass::class, $instance)
        );
    }

    public function testSharedConcreteResolution(): void {
        $this->container->shared(SampleClass::class, SampleClass::class);

        self::assertSame(
            $this->container->get(SampleClass::class),
            $this->container->get(SampleClass::class)
        );
    }

    public function testClosureResolution(): void {
        $this->container->bind('foo', function () {
            return 'bar';
        });

        self::assertSame('bar', $this->container->make('foo'));
    }

    public function testCircularDependencyThrowsException(): void {
        $this->expectException(CircularDependencyException::class);

        $this->container->make(ClassACircularDependency::class);
    }

    public function testResolvingThrowsExceptionIfClassNotFound(): void {
        self::expectException(ResolvingException::class);
        self::expectExceptionMessage('The target [' . Shared::class . '] is not exists.');

        $this->container->bind('foo', Shared::class);
        $this->container->make('foo');
    }

    public function testResolvingThrowsExceptionIfClassIsNotInstantiable(): void {
        self::expectException(ResolvingException::class);
        self::expectExceptionMessage('The target [' . SampleInterface::class . '] is not instantiable.');

        $this->container->bind('foo', SampleInterface::class);
        $this->container->make('foo');
    }
}
