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

            $latestActivityEpoch = $after;

            while ($page <= 10) {

                $query = [
                    'per_page' => $perPage,
                    'page' => $page
                ];

                if ($after) {
                    $query['after'] = $after;
                }

                $response = Http::withToken($account->access_token)
                    ->timeout(30)
                    ->get('https://www.strava.com/api/v3/athlete/activities', $query);

                if (!$response->successful()) {
                    break;
                }

                $activities = $response->json();

                if (empty($activities)) {
                    break;
                }

                foreach ($activities as $activity) {

                    $detailResponse = Http::withToken($account->access_token)
                        ->timeout(30)
                        ->get("https://www.strava.com/api/v3/activities/{$activity['id']}");

                    if (!$detailResponse->successful()) {
                        continue;
                    }

                    $detail = $detailResponse->json();

                    Activity::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'strava_activity_id' => $activity['id']
                        ],
                        [
                            'name' => $detail['name'] ?? null,
                            'sport_type' => $detail['sport_type'] ?? ($detail['type'] ?? null),

                            'distance' => $detail['distance'] ?? null,
                            'moving_time' => $detail['moving_time'] ?? null,
                            'elapsed_time' => $detail['elapsed_time'] ?? null,
                            'total_elevation_gain' => $detail['total_elevation_gain'] ?? null,

                            'average_speed' => $detail['average_speed'] ?? null,
                            'max_speed' => $detail['max_speed'] ?? null,
                            'average_cadence' => $detail['average_cadence'] ?? null,

                            'gear_id' => $detail['gear_id'] ?? null,
                            'polyline' => $detail['map']['summary_polyline'] ?? null,
                            'start_date' => $detail['start_date'] ?? null,

                            'raw_data' => $detail,

                            'last_synced_at' => now()
                        ]
                    );

                    $activityEpoch = strtotime($detail['start_date']);

                    if (!$latestActivityEpoch || $activityEpoch > $latestActivityEpoch) {
                        $latestActivityEpoch = $activityEpoch;
                    }
                }

                if (count($activities) < $perPage) {
                    break;
                }

                $page++;
            }

            // Update cursor sync hanya sekali di akhir
            if ($latestActivityEpoch) {
                $account->strava_last_activity_epoch = $latestActivityEpoch;
            }

            $account->last_activity_sync_at = now();
            $account->save();
        } catch (\Exception $e) {

            Log::error('Error syncing activities', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
