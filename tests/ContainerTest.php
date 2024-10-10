<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Aether\DependencyInjection\Container;
use Aether\DependencyInjection\ContainerInterface;
use Aether\DependencyInjection\Definition\Binding\Alias;
use Aether\DependencyInjection\Definition\Binding\Factory;
use Aether\DependencyInjection\Definition\Binding\Shared;
use Aether\DependencyInjection\Definition\Resolver\DefinitionResolver;
use Aether\DependencyInjection\Definition\Resolver\ParameterResolver;
use Aether\Tests\DependencyInjection\Fixtures\SampleClass;
use Aether\Tests\DependencyInjection\Fixtures\SampleInterface;

#[CoversClass(Alias::class)]
#[CoversClass(Factory::class)]
#[CoversClass(Container::class)]
#[CoversClass(Shared::class)]
#[CoversClass(DefinitionResolver::class)]
#[CoversClass(ParameterResolver::class)]
class ContainerTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testContainerKnowTheEntry(): void
    {
        $this->container->instance(SampleClass::class, new SampleClass());

        self::assertTrue(
            $this->container->has(SampleClass::class)
        );
    }

    public function testContainerDoesNotKnowTheEntry(): void
    {
        self::assertFalse(
            $this->container->has('foo')
        );
    }

    public function testSharedInstanceReturnsTheInstance(): void
    {
        $instance = new SampleClass();

        self::assertSame(
            $instance,
            $this->container->instance(SampleClass::class, $instance)
        );
    }

    public function testSharedConcreteResolution(): void
    {
        $this->container->shared(SampleClass::class, SampleClass::class);

        self::assertSame(
            $this->container->get(SampleClass::class),
            $this->container->get(SampleClass::class)
        );
    }

    public function testClosureResolution(): void
    {
        $this->container->bind('foo', function () {
            return 'bar';
        });

        self::assertSame('bar', $this->container->make('foo'));
    }

    public function testBindingInterfaceToImplementation(): void
    {
        $this->container->bind(SampleInterface::class, SampleClass::class);

        self::assertInstanceOf(
            SampleClass::class,
            $this->container->make(SampleInterface::class)
        );
    }
}
