<?php

/*
 * This file is part of the aether/aether.
 *
 * Copyright (C) 2024 Dominik Szamburski
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Aether\DependencyInjection\Exception;

use Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("Undefined entry `$id`.");
    }
}
