<?php

namespace App\Services\Strava;

use App\Models\Activity;
use App\Models\StravaAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncActivityService
{
    public function syncLatestActivities($userId)
    {
        $account = StravaAccount::where('user_id', $userId)->first();

        if (!$account || !$account->access_token) {
            return;
        }

        try {

            $after = $account->strava_last_activity_epoch;

            $page = 1;
            $perPage = 20;

            $latestEpoch = $after;

            while ($page <= 10) {

                $response = Http::withToken($account->access_token)
                    ->timeout(20)
                    ->get('https://www.strava.com/api/v3/athlete/activities', [
                        'per_page' => $perPage,
                        'page' => $page,
                        'after' => $after
                    ]);

                if (!$response->successful()) {
                    break;
                }

                $activities = $response->json();

                if (empty($activities)) {
                    break;
                }

                foreach ($activities as $activity) {

                    $activityEpoch = strtotime($activity['start_date']);

                    if (!$latestEpoch || $activityEpoch > $latestEpoch) {
                        $latestEpoch = $activityEpoch;
                    }

                    $existing = Activity::where('user_id', $userId)
                        ->where('strava_activity_id', $activity['id'])
                        ->first();

                    $rawHash = md5(json_encode($activity));

                    // ⭐ Jika data sudah ada
                    if ($existing) {

                        if (($existing->raw_hash ?? null) === $rawHash) {
                            continue;
                        }

                        // Update jika berubah
                        $existing->update([
                            'name' => $activity['name'] ?? null,
                            'sport_type' => $activity['sport_type'] ?? ($activity['type'] ?? null),

                            'distance' => $activity['distance'] ?? null,
                            'moving_time' => $activity['moving_time'] ?? null,
                            'elapsed_time' => $activity['elapsed_time'] ?? null,
                            'total_elevation_gain' => $activity['total_elevation_gain'] ?? null,

                            'average_speed' => $activity['average_speed'] ?? null,
                            'max_speed' => $activity['max_speed'] ?? null,
                            'average_cadence' => $activity['average_cadence'] ?? null,

                            'gear_id' => $activity['gear_id'] ?? null,
                            'polyline' => $activity['map']['summary_polyline'] ?? null,

                            'start_date' => $activity['start_date'] ?? null,

                            'raw_data' => $activity,
                            'raw_hash' => $rawHash,

                            'last_synced_at' => now()
                        ]);

                        continue;
                    }

                    // ⭐ Insert jika belum ada
                    Activity::create([
                        'user_id' => $userId,
                        'strava_activity_id' => $activity['id'],

                        'name' => $activity['name'] ?? null,
                        'sport_type' => $activity['sport_type'] ?? ($activity['type'] ?? null),

                        'distance' => $activity['distance'] ?? null,
                        'moving_time' => $activity['moving_time'] ?? null,
                        'elapsed_time' => $activity['elapsed_time'] ?? null,
                        'total_elevation_gain' => $activity['total_elevation_gain'] ?? null,

                        'average_speed' => $activity['average_speed'] ?? null,
                        'max_speed' => $activity['max_speed'] ?? null,
                        'average_cadence' => $activity['average_cadence'] ?? null,

                        'gear_id' => $activity['gear_id'] ?? null,
                        'polyline' => $activity['map']['summary_polyline'] ?? null,

                        'start_date' => $activity['start_date'] ?? null,

                        'raw_data' => $activity,
                        'raw_hash' => $rawHash,

                        'last_synced_at' => now()
                    ]);
                }

                if (count($activities) < $perPage) {
                    break;
                }

                $page++;
            }

            if ($latestEpoch) {
                $account->update([
                    'strava_last_activity_epoch' => $latestEpoch,
                    'last_activity_sync_at' => now()
                ]);
            }
        } catch (\Exception $e) {

            Log::error('Error syncing activities', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
