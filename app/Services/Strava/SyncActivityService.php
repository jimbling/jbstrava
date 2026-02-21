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

        if (!$account) {
            return;
        }

        try {

            $lastSyncedActivityId = $account->strava_last_activity_id;

            $page = 1;
            $perPage = 20; // lebih kecil untuk efficiency

            $stopSync = false;

            while (!$stopSync) {

                $response = Http::withToken($account->access_token)
                    ->get('https://www.strava.com/api/v3/athlete/activities', [
                        'per_page' => $perPage,
                        'page' => $page
                    ]);

                if (!$response->successful()) {
                    break;
                }

                $activities = $response->json();

                if (!is_array($activities) || empty($activities)) {
                    break;
                }

                foreach ($activities as $activity) {

                    // Jika sudah ketemu activity terakhir sync → stop semua sync
                    if (
                        $lastSyncedActivityId &&
                        $activity['id'] == $lastSyncedActivityId
                    ) {

                        $stopSync = true;
                        break;
                    }

                    // Fetch detail activity
                    $detailResponse = Http::withToken($account->access_token)
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

                    // Update tracker activity terbaru
                    if (!$account->strava_last_activity_id) {
                        $account->strava_last_activity_id = $activity['id'];
                        $account->save();
                    }
                }

                if ($stopSync) {
                    break;
                }

                $page++;

                // Safety limit pagination
                if ($page > 10) {
                    break;
                }
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
