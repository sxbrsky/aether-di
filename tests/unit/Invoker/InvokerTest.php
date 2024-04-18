<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Tests\Unit\Invoker;

use IonBytes\Bean\Container;
use IonBytes\Bean\ContainerInterface;
use IonBytes\Bean\Definition\Binding\Alias;
use IonBytes\Bean\Definition\Binding\Shared;
use IonBytes\Bean\Definition\Resolver\DefinitionResolver;
use IonBytes\Bean\Definition\Resolver\ParameterResolver;
use IonBytes\Bean\Exception\RuntimeException;
use IonBytes\Bean\Invoker\Invoker;
use IonBytes\Bean\Tests\Unit\Fixtures\ExtendedSampleClass;
use IonBytes\Bean\Tests\Unit\Fixtures\InvokableClass;
use IonBytes\Bean\Tests\Unit\Fixtures\SampleClass;
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
    private ContainerInterface $container;

    public function setUp(): void {
        $this->container = new Container();
    }

    public function testCallValidClosure(): void {
        $result = $this->container->call(
            static function (string $name): string {
                return $name;
            },
            ['name' => 'johny']
        );

        self::assertSame('johny', $result);
    }

    public function testCallValidCallableArray(): void {
        $result = $this->container->call([InvokableClass::class, 'hello'], ['name' => 'world']);
        self::assertSame('Hello, world', $result);
    }

    public function testCallCallableArrayWithoutMethod(): void {
        $result = $this->container->call([InvokableClass::class], ['name' => 'world']);
        self::assertSame('world', $result);
    }

    public function testCallResolveParametersFromContainer(): void {
        $instance = new ExtendedSampleClass('johny');
        $this->container->shared(ExtendedSampleClass::class, $instance);

        $result = $this->container->call(
            static function (ExtendedSampleClass $sampleClass): string {
                return "Hello, $sampleClass->name";
            }
        );

        self::assertSame("Hello, $instance->name", $result);
    }

    public function testInvalidCallableThrowsException(): void {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Method IonBytes\Bean\Tests\Unit\Fixtures\SampleClass::__invoke() does not exist'
        );

        $this->container->call([SampleClass::class]);
    }
}
