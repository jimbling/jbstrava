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

            $lastSyncTime = $account->last_activity_sync_at;
            $perPage = 20;
            $page = 1;

            while (true) {

                $response = Http::withToken($account->access_token)
                    ->get('https://www.strava.com/api/v3/athlete/activities', [
                        'per_page' => $perPage,
                        'page' => $page
                    ]);

                if (!$response->successful()) break;

                $activities = $response->json();

                if (empty($activities)) break;

                $stopSync = false;

                foreach ($activities as $activity) {

                    $remoteUpdatedAt = $activity['updated_at'] ?? null;

                    // Layer 1 checkpoint stop
                    if ($lastSyncTime && $remoteUpdatedAt <= $lastSyncTime->toISOString()) {
                        $stopSync = true;
                        break;
                    }

                    $localActivity = Activity::where('user_id', $userId)
                        ->where('strava_activity_id', $activity['id'])
                        ->first();

                    $needDetailFetch = false;

                    if (!$localActivity) {
                        $needDetailFetch = true;
                    } else {
                        if (
                            $remoteUpdatedAt &&
                            $localActivity->updated_at < $remoteUpdatedAt
                        ) {
                            $needDetailFetch = true;
                        }
                    }

                    if ($needDetailFetch) {

                        $detailResponse = Http::withToken($account->access_token)
                            ->get("https://www.strava.com/api/v3/activities/{$activity['id']}");

                        if ($detailResponse->successful()) {

                            $detail = $detailResponse->json();

                            Activity::updateOrCreate(
                                [
                                    'user_id' => $userId,
                                    'strava_activity_id' => $activity['id']
                                ],
                                [
                                    'name' => $detail['name'] ?? null,
                                    'sport_type' => $detail['sport_type'] ?? null,
                                    'distance' => $detail['distance'] ?? null,
                                    'moving_time' => $detail['moving_time'] ?? null,
                                    'elapsed_time' => $detail['elapsed_time'] ?? null,
                                    'total_elevation_gain' => $detail['total_elevation_gain'] ?? null,
                                    'polyline' => $detail['map']['summary_polyline'] ?? null,
                                    'raw_data' => $detail,
                                    'last_synced_at' => now()
                                ]
                            );
                        }
                    }
                }

                if ($stopSync) break;

                $page++;

                if ($page > 10) break; // safety breaker
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
