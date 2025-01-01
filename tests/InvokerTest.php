<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024-2025 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DI;

use Aether\Contracts\DI\Exception\RuntimeException;
use Aether\DI\Container;
use Aether\DI\Definition\Binding\Alias;
use Aether\DI\Definition\Binding\Shared;
use Aether\DI\Definition\Resolver\DefinitionResolver;
use Aether\DI\Definition\Resolver\ParameterResolver;
use Aether\DI\Invoker;
use Aether\Tests\DI\Fixtures\ExtendedSampleClass;
use Aether\Tests\DI\Fixtures\InvokableClass;
use Aether\Tests\DI\Fixtures\SampleClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Invoker::class)]
#[CoversClass(Container::class)]
#[CoversClass(Alias::class)]
#[CoversClass(Shared::class)]
#[CoversClass(DefinitionResolver::class)]
#[CoversClass(ParameterResolver::class)]
class InvokerTest extends TestCase
{
    private Container $container;

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
            'Method Aether\Tests\DI\Fixtures\SampleClass::__invoke() does not exist'
        );

        $this->container->call([SampleClass::class]);
    }
}
