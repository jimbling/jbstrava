<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">

        <div class="max-w-6xl mx-auto px-6 space-y-6">

            <!-- Welcome Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-semibold text-gray-800">
                    Welcome back, {{ auth()->user()->name }}
                </h3>

                <p class="text-gray-500 mt-1 text-sm">
                    Manage your integration and activity connection.
                </p>
            </div>

            <!-- Strava Integration Card -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

                <div class="p-6 border-b flex justify-between items-center flex-wrap gap-4">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <span class="text-[#fc4c02]">●</span>
                            Strava Connection Status
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Connect your athlete account to sync activity data.
                        </p>
                    </div>

                    @if(!$strava)
                        <a href="{{ route('strava.connect') }}"
                           class="px-6 py-2.5 bg-[#fc4c02] hover:bg-[#e64500]
                                  text-white rounded-xl shadow-md hover:shadow-lg
                                  transition duration-300 font-semibold">
                            Connect to Strava
                        </a>
                    @else
                        <span class="px-5 py-2 rounded-xl bg-green-100 text-green-700 text-sm font-semibold">
                            Connected ✓
                        </span>
                    @endif

                </div>

                @if($strava)
                    <div class="grid md:grid-cols-2 gap-6 p-6 text-sm">

                        <div class="space-y-2">
                            <p class="text-gray-500">Athlete ID</p>
                            <p class="font-semibold text-gray-800">
                                {{ $strava->strava_athlete_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-gray-500">Scope Permission</p>
                            <p class="font-medium break-all text-gray-800">
                                {{ $strava->scope }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-gray-500">Token Expiry</p>
                            <p class="font-semibold text-gray-800">
                                {{ $strava->expires_at?->format('d M Y H:i') }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-gray-500">Refresh Token</p>
                            <p class="text-green-600 font-semibold">
                                Stored & Encrypted ✓
                            </p>
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
