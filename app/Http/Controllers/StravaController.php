<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\StravaAccount;
use Illuminate\Support\Facades\Auth;

class StravaController extends Controller
{


    /**
     * Redirect to Strava OAuth
     */
    public function connect()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Jika sudah connect, jangan izinkan reconnect
        $exists = StravaAccount::where('user_id', Auth::id())->exists();

        if ($exists) {
            return redirect('/dashboard')
                ->with('success', 'Strava already connected.');
        }

        $query = http_build_query([
            'client_id'       => config('services.strava.client_id'),
            'redirect_uri'    => config('services.strava.redirect'),
            'response_type'   => 'code',
            'approval_prompt' => 'auto',
            'scope'           => 'read,activity:read_all',
        ]);

        return redirect('https://www.strava.com/oauth/authorize?' . $query);
    }

    /**
     * OAuth Callback
     */
    public function callback(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (!$request->has('code')) {
            return redirect('/dashboard')
                ->with('error', 'Authorization failed.');
        }

        try {

            $response = Http::asForm()->post(
                'https://www.strava.com/oauth/token',
                [
                    'client_id'     => config('services.strava.client_id'),
                    'client_secret' => config('services.strava.client_secret'),
                    'code'          => $request->code,
                    'grant_type'    => 'authorization_code',
                ]
            );

            if (!$response->successful()) {
                return redirect('/dashboard')
                    ->with('error', 'Strava API connection failed.');
            }

            $data = $response->json();

            if (!isset($data['access_token'])) {
                return redirect('/dashboard')
                    ->with('error', 'Invalid Strava response.');
            }

            StravaAccount::updateOrCreate(
                [
                    'user_id' => Auth::id()
                ],
                [
                    'strava_athlete_id' => $data['athlete']['id'] ?? null,
                    'access_token'      => $data['access_token'],
                    'refresh_token'     => $data['refresh_token'],
                    'expires_at'        => now()->addSeconds($data['expires_in'] ?? 0),
                    'scope'             => $data['scope'] ?? null,
                ]
            );

            return redirect('/dashboard')
                ->with('success', 'Strava connected successfully!');

        } catch (\Exception $e) {

            return redirect('/dashboard')
                ->with('error', 'Connection error.');
        }
    }
}