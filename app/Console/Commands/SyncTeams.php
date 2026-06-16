<?php

namespace App\Console\Commands;

use App\Models\Character;
use App\Models\Issue;
use App\Models\Team;
use Illuminate\Console\Command;

class SyncTeams extends Command
{
    protected $signature = 'teams:sync';
    protected $description = 'Build the teams table and pivots from the teams JSON column on characters and issues';

    public function handle()
    {
        $this->syncFor(Character::class, 'characters');
        $this->syncFor(Issue::class, 'issues');

        $this->info('Done. Total teams: ' . Team::count());
    }

    private function syncFor(string $modelClass, string $label)
    {
        $records = $modelClass::whereNotNull('teams')->get();
        $this->info("Processing {$records->count()} {$label}...");

        foreach ($records as $record) {
            $teamIds = [];

            foreach ($record->teams ?? [] as $teamData) {
                if (empty($teamData['id']) || empty($teamData['name'])) {
                    continue;
                }

                $team = Team::firstOrCreate(
                    ['comic_vine_id' => $teamData['id']],
                    ['name' => $teamData['name']]
                );

                $teamIds[] = $team->id;
            }

            if (!empty($teamIds)) {
                $record->teamRecords()->syncWithoutDetaching($teamIds);
            }
        }
    }
}