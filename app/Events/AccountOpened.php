<?php

namespace App\Events;

use App\States\AccountState;
use App\Models\Account;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class AccountOpened extends Event
{
    #[StateId(AccountState::class)]
    public int $account_id;

    public string $description;

    public int $initial_deposit = 500;

    public function validate(AccountState $state): bool
    {
        return ($state->balance + $this->initial_deposit) > 0;
    }

    public function apply(AccountState $state): void
    {
        $state->balance = $this->initial_deposit;
    }

    public function handle()
    {
        Account::create([
            'id' => $this->account_id,
            'balance' => $this->initial_deposit,
        ]);
    }
}
