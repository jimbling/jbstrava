<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\StravaAccount;

class DashboardController extends Controller
{
    public function index()
    {
        $strava = StravaAccount::where('user_id', Auth::id())->first();

        return view('dashboard', compact('strava'));
    }
}