<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'strava_activity_id',
        'name',
        'sport_type',
        'distance',
        'moving_time',
        'elapsed_time',
        'total_elevation_gain',
        'average_speed',
        'max_speed',
        'average_cadence',
        'gear_id',
        'polyline',
        'raw_data',
        'start_date',
        'last_synced_at',
        'achievement_flag',
        'is_personal_record',
        'trend_score',
        'strava_last_activity_id',
        'last_activity_sync_at'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'start_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'achievement_flag' => 'boolean',
        'is_personal_record' => 'boolean',
        'distance' => 'float',
        'average_speed' => 'float',
        'max_speed' => 'float',
        'average_cadence' => 'float',
        'total_elevation_gain' => 'float',
        'trend_score' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // App\Models\Activity.php



    // Accessor untuk mengambil data gear dari raw_data
    public function getGearNameAttribute()
    {
        return $this->raw_data['gear']['name'] ?? null;
    }



    // Accessor untuk best efforts
    public function getBestEffortsAttribute()
    {
        return $this->raw_data['best_efforts'] ?? [];
    }

    // Accessor untuk splits metric
    public function getSplitsMetricAttribute()
    {
        return $this->raw_data['splits_metric'] ?? [];
    }

    // Accessor untuk start_latlng
    public function getStartLatlngAttribute()
    {
        return $this->raw_data['start_latlng'] ?? null;
    }

    // Accessor untuk end_latlng
    public function getEndLatlngAttribute()
    {
        return $this->raw_data['end_latlng'] ?? null;
    }

    // Accessor untuk kudos_count
    public function getKudosCountAttribute()
    {
        return $this->raw_data['kudos_count'] ?? 0;
    }

    // Accessor untuk achievement_count
    public function getAchievementCountAttribute()
    {
        return $this->raw_data['achievement_count'] ?? 0;
    }

    // Accessor untuk calories
    public function getCaloriesAttribute()
    {
        return $this->raw_data['calories'] ?? 0;
    }
}
