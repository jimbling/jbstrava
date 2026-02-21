<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StravaAccount extends Model
{
    protected $fillable = [
        'user_id',
        'strava_athlete_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scope',
        'strava_last_activity_epoch'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
