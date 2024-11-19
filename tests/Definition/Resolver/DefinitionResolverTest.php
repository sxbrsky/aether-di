<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\Tests\DI\Definition\Resolver;

use Aether\Contracts\DI\Exception\ContainerException;
use Aether\Contracts\DI\Exception\EntryNotFoundException;
use Aether\DI\Container;
use Aether\DI\Definition\Binding\Alias;
use Aether\DI\Definition\Binding\Scalar;
use Aether\DI\Definition\Exception\CircularDependencyException;
use Aether\DI\Definition\Resolver\DefinitionResolver;
use Aether\DI\Definition\Resolver\ParameterResolver;
use Aether\DI\Definition\State;
use Aether\Tests\DI\Fixtures\ClassACircularDependency;
use Aether\Tests\DI\Fixtures\SampleClass;
use Aether\Tests\DI\Fixtures\SampleInterface;
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
        self::expectExceptionMessage('Undefined entry `test`');

        $this->resolver->make('test');
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
