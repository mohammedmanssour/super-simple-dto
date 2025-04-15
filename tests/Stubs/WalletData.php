<?php

namespace MohammedManssour\DTO\Tests\Stubs;

use MohammedManssour\DTO\Concerns\AsDTO;

class WalletData
{
    use AsDTO;

    public string $type;

    public int $balance;
}
