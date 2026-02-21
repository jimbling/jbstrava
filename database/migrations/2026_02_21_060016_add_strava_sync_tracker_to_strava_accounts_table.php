<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strava_accounts', function (Blueprint $table) {

            // Tracker delta sync activity terakhir
            $table->string('strava_last_activity_id')->nullable()->after('refresh_token');

            // Timestamp sync terakhir
            $table->timestamp('last_activity_sync_at')->nullable()->after('strava_last_activity_id');
        });
    }

    public function down(): void
    {
        Schema::table('strava_accounts', function (Blueprint $table) {

            $table->dropColumn([
                'strava_last_activity_id',
                'last_activity_sync_at'
            ]);
        });
    }
};
