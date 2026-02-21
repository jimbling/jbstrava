<?php

namespace App\Services\Strava;

use App\Models\Activity;
use App\Models\StravaAccount;
use Illuminate\Support\Facades\Http;

class SyncActivityService
{
    public function syncLatestActivities($userId)
    {
        $account = StravaAccount::where('user_id', $userId)->first();

        if (!$account) {
            return;
        }

        $activities = Http::withToken($account->access_token)
            ->get('https://www.strava.com/api/v3/athlete/activities', [
                'per_page' => 50,
                'page' => 1
            ])
            ->json();

        if (!is_array($activities)) {
            return;
        }

        foreach ($activities as $activity) {

            Activity::updateOrCreate(
                [
                    'user_id' => $userId,
                    'strava_activity_id' => $activity['id']
                ],
                [
                    'name' => $activity['name'] ?? null,
                    'sport_type' => $activity['sport_type'] ?? null,
                    'distance' => $activity['distance'] ?? null,
                    'moving_time' => $activity['moving_time'] ?? null,
                    'elapsed_time' => $activity['elapsed_time'] ?? null,
                    'total_elevation_gain' => $activity['total_elevation_gain'] ?? null,
                    'average_speed' => $activity['average_speed'] ?? null,
                    'max_speed' => $activity['max_speed'] ?? null,
                    'polyline' => $activity['map']['summary_polyline'] ?? null,
                    'start_date' => $activity['start_date'] ?? null,
                    'raw_data' => json_encode($activity),
                    'last_synced_at' => now()
                ]
            );
        }
    }
}
