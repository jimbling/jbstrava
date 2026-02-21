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
        'trend_score'
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
}
