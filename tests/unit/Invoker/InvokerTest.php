<?php

/*
 * This file is part of the sxbrsky/dependency-injection.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Sxbrsky\DependencyInjection\Tests\Unit\Invoker;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sxbrsky\DependencyInjection\Container;
use Sxbrsky\DependencyInjection\ContainerInterface;
use Sxbrsky\DependencyInjection\Definition\Binding\Alias;
use Sxbrsky\DependencyInjection\Definition\Binding\Shared;
use Sxbrsky\DependencyInjection\Definition\Resolver\DefinitionResolver;
use Sxbrsky\DependencyInjection\Definition\Resolver\ParameterResolver;
use Sxbrsky\DependencyInjection\Exception\RuntimeException;
use Sxbrsky\DependencyInjection\Invoker\Invoker;
use Sxbrsky\DependencyInjection\Tests\Unit\Fixtures\ExtendedSampleClass;
use Sxbrsky\DependencyInjection\Tests\Unit\Fixtures\InvokableClass;
use Sxbrsky\DependencyInjection\Tests\Unit\Fixtures\SampleClass;

#[CoversClass(Invoker::class)]
#[CoversClass(Container::class)]
#[CoversClass(Alias::class)]
#[CoversClass(Shared::class)]
#[CoversClass(DefinitionResolver::class)]
#[CoversClass(ParameterResolver::class)]
class InvokerTest extends TestCase
{
    private ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testCallValidClosure(): void
    {
        $result = $this->container->call(
            static function (string $name): string {
                return $name;
            },
            ['name' => 'johny']
        );

        self::assertSame('johny', $result);
    }

    public function testCallValidCallableArray(): void
    {
        $result = $this->container->call([InvokableClass::class, 'hello'], ['name' => 'world']);
        self::assertSame('Hello, world', $result);
    }

    public function testCallCallableArrayWithoutMethod(): void
    {
        $result = $this->container->call([InvokableClass::class], ['name' => 'world']);
        self::assertSame('world', $result);
    }

    public function testCallResolveParametersFromContainer(): void
    {
        $instance = new ExtendedSampleClass('johny');
        $this->container->shared(ExtendedSampleClass::class, $instance);

        $result = $this->container->call(
            static function (ExtendedSampleClass $sampleClass): string {
                return "Hello, $sampleClass->name";
            }
        );

        self::assertSame("Hello, $instance->name", $result);
    }

    public function testInvalidCallableThrowsException(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Method Sxbrsky\DependencyInjection\Tests\Unit\Fixtures\SampleClass::__invoke() does not exist'
        );

        $this->container->call([SampleClass::class]);
    }
}
