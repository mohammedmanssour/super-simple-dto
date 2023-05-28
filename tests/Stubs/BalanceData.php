<?php

namespace MohammedManssour\DTO\Tests\Stubs;

use MohammedManssour\DTO\Concerns\AsDTO;

class BalanceData
{
    use AsDTO;

    public float $bitcoin;

    public int $usdollar;
}
