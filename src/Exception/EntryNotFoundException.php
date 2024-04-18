<?php

/*
 * This file is part of the ionbytes/bean.
 *
 * Copyright (C) 2024 IonBytes Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace IonBytes\Bean\Exception;

use Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public function __construct(string $id) {
        parent::__construct("Undefined entry `$id`.");
    }
}
