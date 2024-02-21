<?php

namespace App\Events;

use App\Models\Account;
use App\States\AccountState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\CommitsImmediately;
use Thunk\Verbs\Event;

class BalanceIncreased extends Event implements CommitsImmediately
{
    #[StateId(AccountState::class)]
    public int $account_id;

    public string $description;

    public int $credits = 0;


    public function apply(AccountState $state): void
    {
        $state->balance += $this->credits;
    }

    public function handle(): void
    {
        Account::find($this->account_id)
            ->update([
                'balance' => $this->state()->balance,
            ]);
    }
}
