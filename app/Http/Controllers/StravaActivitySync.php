<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Strava\SyncActivityService;
use Illuminate\Http\Request;

class StravaActivitySync extends Controller
{
    public function sync(SyncActivityService $service)
{
    $service->syncLatestActivities(auth()->id());

    return back()->with('success','Activity synced successfully');
}
}
