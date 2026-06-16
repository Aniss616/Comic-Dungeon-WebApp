<?php

namespace App\Console\Commands;

use App\Models\Issue;
use App\Models\Location;
use Illuminate\Console\Command;

class SyncLocations extends Command
{
    protected $signature = 'locations:sync';
    protected $description = 'Build the locations table and pivot from the locations JSON column on issues';

    public function handle()
    {
        $issues = Issue::whereNotNull('locations')->get();
        $this->info("Processing {$issues->count()} issues...");

        foreach ($issues as $issue) {
            $locationIds = [];

            foreach ($issue->locations ?? [] as $locationData) {
                if (empty($locationData['id']) || empty($locationData['name'])) {
                    continue;
                }

                $location = Location::firstOrCreate(
                    ['comic_vine_id' => $locationData['id']],
                    ['name' => $locationData['name']]
                );

                $locationIds[] = $location->id;
            }

            if (!empty($locationIds)) {
                $issue->locationRecords()->syncWithoutDetaching($locationIds);
            }
        }

        $this->info('Done. Total locations: ' . Location::count());
    }
}