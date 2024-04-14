<?php

/*
 * This file is part of the ionbytes/container.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Container\Tests\Unit\Invoker;

use IonBytes\Container\Container;
use IonBytes\Container\ContainerInterface;
use IonBytes\Container\Definition\Binding\Alias;
use IonBytes\Container\Definition\Binding\Shared;
use IonBytes\Container\Definition\Resolver\DefinitionResolver;
use IonBytes\Container\Definition\Resolver\ParameterResolver;
use IonBytes\Container\Exception\RuntimeException;
use IonBytes\Container\Invoker\Invoker;
use IonBytes\Container\Tests\Unit\Fixtures\ExtendedSampleClass;
use IonBytes\Container\Tests\Unit\Fixtures\InvokableClass;
use IonBytes\Container\Tests\Unit\Fixtures\SampleClass;
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
            'Method IonBytes\Container\Tests\Unit\Fixtures\SampleClass::__invoke() does not exist'
        );

        $this->container->call([SampleClass::class]);
    }
}
