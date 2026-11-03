<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Institution;
use App\Models\InstitutionCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTvetDirectory extends Command
{
    protected $signature = 'tvet:import {--path= : Directory JSON path}';

    protected $description = 'Import the TVET Madani snapshot without changing existing account memberships or campus pins';

    public function handle(): int
    {
        $path = $this->option('path') ?: database_path('data/tvet-directory.json');
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            foreach ($data as $row) {
                $agency = Agency::updateOrCreate(['source_id' => (string) $row['id']], ['name' => $row['name']]);
                foreach ($row['institutions'] as $name => $courses) {
                    $institution = Institution::updateOrCreate(['code' => 'TVET-'.$row['id'].'-'.substr(hash('sha256', $name), 0, 20)], ['name' => $name, 'agency_id' => $agency->id]);
                    $metadata = $row['locations'][$name] ?? [];
                    if ($metadata) {
                        $institution->update($metadata);
                    }
                    foreach ($courses as $course) {
                        parse_str(parse_url($course['url'], PHP_URL_QUERY), $query);
                        InstitutionCourse::updateOrCreate(['source_id' => (string) $query['id_kursus']], ['institution_id' => $institution->id, 'name' => $course['name'], 'source_url' => $course['url']]);
                    }
                }
            }
        });
        $this->info(Agency::count().' agencies, '.Institution::whereNotNull('agency_id')->count().' institutions, '.InstitutionCourse::count().' courses imported.');

        return self::SUCCESS;
    }
}
