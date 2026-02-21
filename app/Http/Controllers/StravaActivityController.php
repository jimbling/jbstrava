<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\Strava\SyncActivityService;
use Illuminate\Http\Request;

class StravaActivityController extends Controller
{
    public function index()
{
    $activities = Activity::where('user_id', auth()->id())
        ->orderByDesc('start_date')
        ->paginate(12);

    return view('strava.activity', compact('activities'));
}

public function sync(SyncActivityService $service)
{
    $service->syncLatestActivities(auth()->id());

    return back()->with('success','Activity synced successfully');
}

public function show($id)
{
    $activity = Activity::where('user_id', auth()->id())
        ->findOrFail($id);

    $coordinates = [];

    if ($activity->polyline) {
        $coordinates = \App\Helpers\PolylineHelper::decode(
            $activity->polyline
        );
    }

    return view('strava.activity-detail', compact(
        'activity',
        'coordinates'
    ));
}

}
