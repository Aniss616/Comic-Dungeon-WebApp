<?php

namespace App\Console\Commands;

use App\Models\Issue;
use App\Models\StoryArc;
use Illuminate\Console\Command;

class SyncStoryArcs extends Command
{
    protected $signature   = 'story-arcs:sync';
    protected $description = 'Populate story_arcs table from issue story_arc_credits JSON';

    public function handle(): int
    {
        $issues = Issue::whereNotNull('story_arc_credits')->get();

        $this->info("Processing {$issues->count()} issues...");
        $bar = $this->output->createProgressBar($issues->count());

        foreach ($issues as $issue) {
            $credits = $issue->story_arc_credits; // already cast to array

            if (empty($credits)) {
                $bar->advance();
                continue;
            }

            $arcIds = [];

            foreach ($credits as $credit) {
                // story_arc_credits entries: ['id' => int, 'name' => string, 'api_detail_url' => ...]
                if (empty($credit['id']) || empty($credit['name'])) continue;

                $arc = StoryArc::firstOrCreate(
                    ['comic_vine_id' => $credit['id']],
                    ['name'          => $credit['name']]
                );

                $arcIds[] = $arc->id;
            }

            if (!empty($arcIds)) {
                $issue->storyArcs()->syncWithoutDetaching($arcIds);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Story arcs synced. Total: ' . StoryArc::count());

        return 0;
    }
}