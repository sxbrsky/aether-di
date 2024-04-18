<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Tests\Unit\Definition\Resolver;

use IonBytes\Bean\Container;
use IonBytes\Bean\Definition\Binding\Alias;
use IonBytes\Bean\Definition\Binding\Scalar;
use IonBytes\Bean\Definition\Exception\CircularDependencyException;
use IonBytes\Bean\Definition\Resolver\DefinitionResolver;
use IonBytes\Bean\Definition\Resolver\ParameterResolver;
use IonBytes\Bean\Definition\State;
use IonBytes\Bean\Exception\ContainerException;
use IonBytes\Bean\Exception\EntryNotFoundException;
use IonBytes\Bean\Tests\Unit\Fixtures\ClassACircularDependency;
use IonBytes\Bean\Tests\Unit\Fixtures\SampleClass;
use IonBytes\Bean\Tests\Unit\Fixtures\SampleInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Alias::class)]
#[CoversClass(Scalar::class)]
#[CoversClass(Container::class)]
#[CoversClass(DefinitionResolver::class)]
#[CoversClass(ParameterResolver::class)]
#[CoversClass(EntryNotFoundException::class)]
class DefinitionResolverTest extends TestCase
{
    private State $state;
    private DefinitionResolver $resolver;

    public function setUp(): void {
        $this->state = new State();
        $this->resolver = new DefinitionResolver($this->state, new Container());
    }

    public function testReturnInstanceIfWasPreviouslyResolved(): void {
        $this->state->instances[SampleClass::class] = new SampleClass();

        self::assertInstanceOf(
            SampleClass::class,
            $this->resolver->make(SampleClass::class)
        );
    }

    public function testAutowiringAbstractWhileBindingNotFound(): void {
        self::assertInstanceOf(
            SampleClass::class,
            $this->resolver->make(SampleClass::class)
        );
    }

    public function testCircularDependencyThrowsException(): void {
        $this->expectException(CircularDependencyException::class);
        $this->resolver->make(ClassACircularDependency::class);
    }

    public function testResolverThrowsExceptionIfClassNotFound(): void {
        self::expectException(EntryNotFoundException::class);
        self::expectExceptionMessage('Undefined entry `' . Shared::class . '`');

        $this->resolver->make(Shared::class);
    }

    public function testResolverThrowsExceptionIfClassIsNotInstantiable(): void {
        self::expectException(ContainerException::class);
        self::expectExceptionMessage(SampleInterface::class . ' is not instantiable.');

        $this->resolver->make(SampleInterface::class);
    }

    public function testResolverCanReturnScalarValue(): void {
        $this->state->bindings['foo'] = new Scalar('bar');

        self::assertSame(
            'bar',
            $this->resolver->make('foo')
        );
    }
}
