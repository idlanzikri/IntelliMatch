<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GrantAdminCommand extends Command
{
    protected $signature = 'app:grant-admin {email : Email of an existing account}';

    protected $description = 'Grant administrator access to an existing account';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('Account not found. Register the account first.');

            return self::FAILURE;
        }
        $user->is_admin = true;
        if ($user->role !== 'superadmin') {
            $user->role = 'admin';
        }
        $user->save();
        $this->info('Administrator access granted. Email verification is still required.');

        return self::SUCCESS;
    }
}
