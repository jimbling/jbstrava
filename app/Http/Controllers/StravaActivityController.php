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
        dd('sync controller hit');

        $service->syncLatestActivities(auth()->id());

        return back()->with('success', 'Activity synced successfully');
    }

    public function show($id)
    {
        $activity = Activity::where('user_id', auth()->id())
            ->findOrFail($id);

        $activityData = $activity;

        $rawData = $activity->raw_data ?? [];

        // Gear data
        if (!empty($rawData['gear'])) {
            $activityData['gear'] = $rawData['gear'];
        }

        // Other metadata
        $activityData['start_latlng'] = $rawData['start_latlng'] ?? null;
        $activityData['end_latlng'] = $rawData['end_latlng'] ?? null;
        $activityData['kudos_count'] = $rawData['kudos_count'] ?? 0;
        $activityData['achievement_count'] = $rawData['achievement_count'] ?? 0;
        $activityData['calories'] = $rawData['calories'] ?? 0;
        $activityData['elev_high'] = $rawData['elev_high'] ?? 0;
        $activityData['elev_low'] = $rawData['elev_low'] ?? 0;
        $activityData['device_name'] = $rawData['device_name'] ?? 'Strava App';
        $activityData['timezone'] = $rawData['timezone'] ?? null;
        $activityData['visibility'] = $rawData['visibility'] ?? 'everyone';
        $activityData['trainer'] = $rawData['trainer'] ?? false;
        $activityData['commute'] = $rawData['commute'] ?? false;
        $activityData['description'] = $rawData['description'] ?? '';
        $activityData['photos'] = $rawData['photos'] ?? null;
        $activityData['segment_efforts'] = $rawData['segment_efforts'] ?? [];
        $activityData['laps'] = $rawData['laps'] ?? [];
        $activityData['splits_standard'] = $rawData['splits_standard'] ?? [];

        $coordinates = [];

        if ($activity->polyline) {
            $coordinates = \App\Helpers\PolylineHelper::decode(
                $activity->polyline
            );
        }

        // dd($activity->raw_data);

        return view('strava.activity-detail', [
            'activity' => $activityData,
            'coordinates' => $coordinates
        ]);
    }
}
