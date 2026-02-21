<?php

namespace App\Jobs;

use App\Services\Strava\SyncActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncStravaActivityJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        app(SyncActivityService::class)
            ->syncLatestActivities($this->userId);
    }
}
