<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Institution;
use App\Models\Vacancy;
use App\Support\DemoLocation;
use Illuminate\Console\Command;

class SeedDemoLocations extends Command
{
    protected $signature = 'tvet:seed-demo-locations';

    protected $description = 'Assign reproducible random city demo pins within each state; vacancies inherit company pins';

    public function handle(): int
    {
        $counts = ['companies' => 0, 'institutions' => 0, 'vacancies' => 0];
        $skipped = [];
        $assign = function ($record, ?array $location, string $type) use (&$counts, &$skipped) {
            if ($record->latitude !== null && $record->longitude !== null && ! $record->location_is_approximate) {
                return;
            }
            if (! $location) {
                $skipped[] = $type.' #'.$record->id.' '.$record->name;

                return;
            }
            $record->forceFill(['latitude' => $location['latitude'], 'longitude' => $location['longitude'], 'location_is_approximate' => true])->save();
            $counts[$type]++;
        };
        Company::chunkById(200, function ($rows) use ($assign) {
            foreach ($rows as $company) {
                $assign($company, DemoLocation::for($company->location, 'company:'.$company->id), 'companies');
            }
        });
        Institution::chunkById(200, function ($rows) use ($assign) {
            foreach ($rows as $institution) {
                $seed = 'institution:'.$institution->id;
                $location = DemoLocation::for($institution->state, $seed) ?? DemoLocation::for($institution->address, $seed) ?? DemoLocation::for($institution->name, $seed);
                $assign($institution, $location, 'institutions');
            }
        });
        Vacancy::with('company')->chunkById(200, function ($rows) use (&$counts, &$skipped) {
            foreach ($rows as $vacancy) {
                if ($vacancy->arrangement === 'remote') {
                    continue;
                }
                if ($vacancy->latitude !== null && $vacancy->longitude !== null && ! $vacancy->location_is_approximate) {
                    continue;
                }
                $company = $vacancy->company;
                if ($company?->latitude === null || $company?->longitude === null) {
                    $skipped[] = 'vacancy #'.$vacancy->id;

                    continue;
                }
                $vacancy->forceFill(['latitude' => $company->latitude, 'longitude' => $company->longitude, 'location_is_approximate' => $company->location_is_approximate])->save();
                $counts['vacancies']++;
            }
        });
        foreach ($counts as $type => $count) {
            $this->info("{$type}: {$count} demo locations assigned");
        }
        foreach ($skipped as $row) {
            $this->warn('State unknown; skipped '.$row);
        }

        return self::SUCCESS;
    }
}
