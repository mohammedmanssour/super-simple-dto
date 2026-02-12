<?php

namespace MohammedManssour\DTO\Tests\Stubs;
use MohammedManssour\DTO\Concerns\AsDTO;

class Secrets {
    use AsDTO;

    public string $id;

    public string $secret;
}