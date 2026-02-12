<?php

namespace MohammedManssour\DTO\Tests\Stubs;

use Carbon\Carbon;
use MohammedManssour\DTO\Concerns\AsDTO;
use MohammedManssour\DTO\Support\MapInto;

class UserData
{
    use AsDTO;

    public string $name;

    public ?string $email;

    public BalanceData $balance;

    public Status $status;

    public Carbon $registered_at;

    #[MapInto(WalletData::class)]
    public ?array $wallets;

    protected Secrets $secrets;

    public function setSecrets($secrets) {
        $this->secrets = $secrets instanceof Secrets ? $secrets : Secrets::fromArray($secrets);
    }

    public function getSecrets(): Secrets {
        return $this->secrets;
    }

    public function setRegisteredAt($value)
    {
        $this->registered_at = Carbon::parse($value);
    }
}
