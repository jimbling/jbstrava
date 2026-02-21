<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StravaActivityController;
use App\Http\Controllers\StravaController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::get('/strava/activity', [StravaActivityController::class,'index'])
    ->middleware(['auth'])
    ->name('strava.activity');

Route::post('/strava/sync', [StravaActivityController::class,'sync'])
    ->middleware('auth')
    ->name('strava.sync');

    Route::get('/strava/activity/{id}', [StravaActivityController::class,'show'])
    ->middleware('auth')
    ->name('strava.activity.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/strava/connect', [StravaController::class, 'connect'])->name('strava.connect');
    Route::get('/strava/callback', [StravaController::class, 'callback'])->name('strava.callback');
});

require __DIR__.'/auth.php';
