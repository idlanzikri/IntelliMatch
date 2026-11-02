<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Console\Command;

class LinkInstitutionCommand extends Command
{
    protected $signature = 'app:link-institution {email} {code}';

    protected $description = 'Link an existing student or institution staff account to an institution code';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->whereIn('role', ['student', 'institution'])->first();
        $institution = Institution::where('code', $this->argument('code'))->first();

        if (! $user || ! $institution) {
            $this->error('Eligible account or institution code not found.');

            return self::FAILURE;
        }
        
        $user->institution_id = $institution->id;
        $user->save();
        $this->info('Institution link updated.');

        return self::SUCCESS;
    }
}
