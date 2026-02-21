<?php

namespace App\Jobs;

use App\Services\Strava\SyncActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncStravaActivityJob implements ShouldQueue
{
    use Queueable;

    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        app(SyncActivityService::class)
            ->syncLatestActivities($this->userId);
    }
}
