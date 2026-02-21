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

            $latestEpoch = $account->strava_last_activity_epoch;
            $page = 1;
            $perPage = 20;

            while (true) {

                $params = [
                    'per_page' => $perPage,
                    'page' => $page
                ];

                if ($latestEpoch) {
                    $params['after'] = $latestEpoch;
                }

                $response = Http::withToken($account->access_token)
                    ->timeout(60)
                    ->get(
                        'https://www.strava.com/api/v3/athlete/activities',
                        $params
                    );

                if (!$response->successful()) {

                    Log::warning('Strava API failed', [
                        'user_id' => $userId,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    break;
                }

                $activities = $response->json();

                if (empty($activities)) {
                    break;
                }

                foreach ($activities as $activity) {

                    try {

                        $result['synced']++;

                        $activityEpoch = strtotime($activity['start_date']);

                        if (!$latestEpoch || $activityEpoch > $latestEpoch) {
                            $latestEpoch = $activityEpoch;
                        }

                        /*
                        --------------------------------
                        FETCH DETAIL ACTIVITY
                        --------------------------------
                        */
                        $detailResponse = Http::withToken($account->access_token)
                            ->timeout(60)
                            ->get(
                                "https://www.strava.com/api/v3/activities/{$activity['id']}"
                            );

                        if (!$detailResponse->successful()) {
                            continue;
                        }

                        $detail = $detailResponse->json();

                        /*
                        --------------------------------
                        STORAGE ANALYTICS DATA
                        --------------------------------
                        */

                        $rawHash = md5(serialize($detail));

                        $existing = Activity::where('user_id', $userId)
                            ->where('strava_activity_id', $activity['id'])
                            ->first();

                        $data = [
                            'user_id' => $userId,
                            'strava_activity_id' => $activity['id'],

                            'name' => $detail['name'] ?? null,
                            'sport_type' => $detail['sport_type'] ?? ($detail['type'] ?? null),

                            'distance' => $detail['distance'] ?? null,
                            'moving_time' => $detail['moving_time'] ?? null,
                            'elapsed_time' => $detail['elapsed_time'] ?? null,
                            'total_elevation_gain' => $detail['total_elevation_gain'] ?? null,

                            'average_speed' => $detail['average_speed'] ?? null,
                            'max_speed' => $detail['max_speed'] ?? null,
                            'average_cadence' => $detail['average_cadence'] ?? null,

                            'gear_id' => $detail['gear']['id'] ?? ($detail['gear_id'] ?? null),
                            'polyline' => $detail['map']['summary_polyline'] ?? null,

                            'start_date' => $detail['start_date'] ?? null,

                            'raw_data' => $detail,
                            'raw_hash' => $rawHash,

                            'last_synced_at' => now()
                        ];

                        if ($existing) {

                            if (($existing->raw_hash ?? null) === $rawHash) {
                                continue;
                            }

                            $existing->update($data);
                            $result['updated']++;
                        } else {

                            Activity::create($data);
                            $result['inserted']++;
                        }
                    } catch (\Exception $e) {

                        Log::error('Sync activity item failed', [
                            'user_id' => $userId,
                            'activity_id' => $activity['id'] ?? null,
                            'error' => $e->getMessage()
                        ]);

                        continue;
                    }
                }

                if (count($activities) < $perPage) {
                    break;
                }

                $page++;

                if ($page > 10) {
                    break;
                }
            }

            if (isset($latestEpoch)) {

                $account->update([
                    'strava_last_activity_epoch' => $latestEpoch,
                    'last_activity_sync_at' => now()
                ]);
            }

            return $result;
        } catch (\Exception $e) {

            Log::error('Sync activity fatal error', [
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
