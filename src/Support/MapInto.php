<?php

namespace MohammedManssour\DTO\Support;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapInto
{
    public function __construct(public string $class)
    {
    }
}