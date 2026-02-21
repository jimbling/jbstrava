<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strava_accounts', function (Blueprint $table) {
            $table->bigInteger('strava_last_activity_epoch')
                ->nullable()
                ->after('access_token');
        });
    }

    public function down(): void
    {
        Schema::table('strava_accounts', function (Blueprint $table) {
            $table->dropColumn('strava_last_activity_epoch');
        });
    }
};
