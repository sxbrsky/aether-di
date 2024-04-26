<?php

/*
 * This file is part of the nuldark/bean.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Bean\Tests\Unit\Definition\Resolver;

use Bean\Container;
use Bean\Definition\Binding\Alias;
use Bean\Definition\Binding\Scalar;
use Bean\Definition\Exception\CircularDependencyException;
use Bean\Definition\Resolver\DefinitionResolver;
use Bean\Definition\Resolver\ParameterResolver;
use Bean\Definition\State;
use Bean\Exception\ContainerException;
use Bean\Exception\EntryNotFoundException;
use Bean\Tests\Unit\Fixtures\ClassACircularDependency;
use Bean\Tests\Unit\Fixtures\SampleClass;
use Bean\Tests\Unit\Fixtures\SampleInterface;
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

    public function setUp(): void
    {
        $this->state = new State();
        $this->resolver = new DefinitionResolver($this->state, new Container());
    }

    public function testReturnInstanceIfWasPreviouslyResolved(): void
    {
        $this->state->instances[SampleClass::class] = new SampleClass();

        self::assertInstanceOf(
            SampleClass::class,
            $this->resolver->make(SampleClass::class)
        );
    }

    public function testAutowiringAbstractWhileBindingNotFound(): void
    {
        self::assertInstanceOf(
            SampleClass::class,
            $this->resolver->make(SampleClass::class)
        );
    }

    public function testCircularDependencyThrowsException(): void
    {
        $this->expectException(CircularDependencyException::class);
        $this->resolver->make(ClassACircularDependency::class);
    }

    public function testResolverThrowsExceptionIfClassNotFound(): void
    {
        self::expectException(EntryNotFoundException::class);
        self::expectExceptionMessage('Undefined entry `' . Shared::class . '`');

        $this->resolver->make(Shared::class);
    }

    public function testResolverThrowsExceptionIfClassIsNotInstantiable(): void
    {
        self::expectException(ContainerException::class);
        self::expectExceptionMessage(SampleInterface::class . ' is not instantiable.');

        $this->resolver->make(SampleInterface::class);
    }

    public function testResolverCanReturnScalarValue(): void
    {
        $this->state->bindings['foo'] = new Scalar('bar');

        self::assertSame(
            'bar',
            $this->resolver->make('foo')
        );
    }
}
