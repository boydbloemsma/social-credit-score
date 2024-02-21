<?php

namespace App\Console\Commands;

use App\Events\BalanceDecreased;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class subBalance extends Command implements PromptsForMissingInput
{
    private int $default_credits = 1;

    protected $signature = 'balance:sub {account} {description} {credits?} ';

    protected $description = 'Subtract a specified amount of credits from an account.';

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'account' => ['From which account should the credits be subtracted?', 'E.g. boyd.bloemsma'],
            'description' => ['Under what description should the credits be subtracted?', 'E.g. Was very rude today.'],
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

        BalanceDecreased::fire(
            account_id: $account->id,
            credits: (int) $credits,
            description: $description,
        );

        $new_balance = $account->balance -= $credits;

        $this->info("Balance for $account->name is now $new_balance.");
    }
}
