<?php

namespace MohammedManssour\DTO\Tests\Stubs;

use MohammedManssour\DTO\Concerns\AsDTO;

class UserData
{
    use AsDTO;

    public string $name;

    public string $email;

    public BalanceData $balance;

    public Status $status;

    public function balance($value)
    {
        return $this->balance = BalanceData::fromArray($value);
    }

    public function status($value)
    {
        return $this->status = Status::from($value);
    }
}
