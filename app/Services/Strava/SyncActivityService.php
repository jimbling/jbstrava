<?php

namespace App\Services\Strava;

use App\Models\Activity;
use App\Models\StravaAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncActivityService
{
    public function syncLatestActivities($userId): array
    {
        $account = StravaAccount::where('user_id', $userId)->first();

        if (!$account || !$account->access_token) {
            return [
                'status' => 'error',
                'message' => 'Account or token not found',
                'synced' => 0,
                'inserted' => 0,
                'updated' => 0
            ];
        }

        $result = [
            'status' => 'success',
            'message' => 'sync completed',
            'synced' => 0,
            'inserted' => 0,
            'updated' => 0
        ];

        try {

            $after = $account->strava_last_activity_epoch;
            $page = 1;
            $perPage = 20;

            $latestEpoch = $after;

            while ($page <= 10) {

                $params = [
                    'per_page' => $perPage,
                    'page' => $page
                ];

                // ⭐ IMPORTANT: only send after if exists
                if ($after) {
                    $params['after'] = $after;
                }

                $response = Http::withToken($account->access_token)
                    ->timeout(20)
                    ->get(
                        'https://www.strava.com/api/v3/athlete/activities',
                        $params
                    );

                if (!$response->successful()) {
                    break;
                }

                $activities = $response->json();

                if (empty($activities)) {
                    break;
                }

                foreach ($activities as $activity) {

                    $result['synced']++;

                    $activityEpoch = strtotime($activity['start_date']);

                    if (!$latestEpoch || $activityEpoch > $latestEpoch) {
                        $latestEpoch = $activityEpoch;
                    }

                    $rawHash = md5(json_encode($activity));

                    $existing = Activity::where('user_id', $userId)
                        ->where('strava_activity_id', $activity['id'])
                        ->first();

                    // ========================
                    // UPDATE EXISTING
                    // ========================
                    if ($existing) {

                        if (($existing->raw_hash ?? null) === $rawHash) {
                            continue;
                        }

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

                        $result['updated']++;

                        continue;
                    }

                    // ========================
                    // INSERT NEW
                    // ========================
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

                    $result['inserted']++;
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

            return $result;
        } catch (\Exception $e) {

            Log::error('Error syncing activities', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'synced' => $result['synced'] ?? 0,
                'inserted' => $result['inserted'] ?? 0,
                'updated' => $result['updated'] ?? 0
            ];
        }
    }
}
