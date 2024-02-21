<?php

namespace App\Console\Commands;

use App\Events\AccountOpened;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Validator;

class newBalance extends Command implements PromptsForMissingInput
{
    protected $signature = 'balance:new {account} ';

    protected $description = 'Create a new account to store credits.';

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'account' => ['Name of the account holder.', 'E.g. boyd.bloemsma'],
        ];
    }

    public function handle(): void
    {
        $validator = Validator::make($this->arguments(), [
            'account' => 'required|string|unique:accounts,name|max:255',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed, check input.');
            return;
        }

        $account_name = $this->argument('account');

        AccountOpened::fire(
            account_id: snowflake_id(),
            account_name: $account_name,
        );

        $this->info("Account $account_name created.");
    }
}
