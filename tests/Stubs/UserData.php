<?php

namespace MohammedManssour\DTO\Tests\Stubs;

use Carbon\Carbon;
use MohammedManssour\DTO\Concerns\AsDTO;

class UserData
{
    use AsDTO;

    public string $name;

    public ?string $email;

    public BalanceData $balance;

    public Status $status;

    public Carbon $registered_at;

    public function balance($value)
    {
        $this->balance = BalanceData::fromArray($value);
    }

    public function status($value)
    {
        $this->status = Status::from($value);
    }

    public function registered_at($value)
    {
        $this->registered_at = Carbon::parse($value);
    }
}
