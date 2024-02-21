<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Leaderboard extends Command implements PromptsForMissingInput
{
    protected $signature = 'balance:leaderboard';

    protected $description = "View every account's credits in a leaderboard.";

    public function handle(): void
    {
        $accounts = Account::query()
            ->orderBy('balance', 'desc')
            ->get(['name', 'balance']);

        $this->table(
            ['Name', 'Credits'],
            $accounts,
        );
    }
}
