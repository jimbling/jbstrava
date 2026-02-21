<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unsignedBigInteger('strava_activity_id')->unique();

    $table->string('name')->nullable();
    $table->string('sport_type')->nullable();

    $table->float('distance')->nullable();
    $table->integer('moving_time')->nullable();
    $table->integer('elapsed_time')->nullable();

    $table->float('total_elevation_gain')->nullable();

    // Performance metrics
    $table->float('average_speed')->nullable();
    $table->float('max_speed')->nullable();
    $table->float('average_cadence')->nullable()->default(null);

    // Advanced analytics (premium layer 🔥)
    $table->float('pace_avg')->nullable();
    $table->float('heart_rate_avg')->nullable()->nullable();

    $table->string('gear_id')->nullable();

    // Route map
    $table->text('polyline')->nullable();

    // Raw backup data
    $table->json('raw_data')->nullable();

    // Achievement analytics
    $table->boolean('achievement_flag')->default(false);
    $table->boolean('is_personal_record')->default(false);

    // Trend scoring (0 - 100 scale recommended)
    $table->float('trend_score')->nullable();

    $table->dateTime('start_date')->index();

    $table->timestamp('last_synced_at')->nullable();

    $table->timestamps();

    $table->index(['user_id', 'start_date']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
