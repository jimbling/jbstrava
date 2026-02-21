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
    Schema::create('strava_accounts', function (Blueprint $table) {
        $table->id();

        // Relasi ke users
        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        // ID athlete dari Strava
        $table->unsignedBigInteger('strava_athlete_id')->index();

        // OAuth tokens
        $table->text('access_token');
        $table->text('refresh_token');
        $table->timestamp('expires_at');

        // Scope permission (opsional)
        $table->string('scope')->nullable();

        $table->timestamps();

        // 1 user hanya boleh 1 akun strava
        $table->unique('user_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strava_accounts');
    }
};
