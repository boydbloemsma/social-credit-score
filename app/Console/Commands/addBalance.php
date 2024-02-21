<?php

namespace App\Console\Commands;

use App\Events\BalanceIncreased;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class addBalance extends Command implements PromptsForMissingInput
{
    private int $default_credits = 1;

    protected $signature = 'balance:add {account} {description} {credits?} ';

    protected $description = 'Add a specified amount of credits to an account.';

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'account' => ['Which account should receive the credits?', 'E.g. boyd.bloemsma'],
            'description' => ['Under what description should the credits be added?', 'E.g. Was very helpful today.'],
        ];
    }

    public function handle(): void
    {
        $validator = Validator::make($this->arguments(), [
            'account' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'credits' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed, check input.');
            return;
        }

        $account_name = $this->argument('account');
        $credits = $this->argument('credits') ?? $this->default_credits;
        $description = $this->argument('description');

        try {
            $account = Account::where('name', $account_name)->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error('No account found under that name, please add the account using `balance:new`.');
            return;
        }

        BalanceIncreased::fire(
            account_id: $account->id,
            credits: (int) $credits,
            description: $description,
        );

        $new_balance = $account->balance += $credits;

        $this->info("Balance for $account->name is now $new_balance.");
    }
}
