<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            <!-- HEADER SECTION -->
            <!-- HEADER SECTION -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $activity['name'] ?? 'Unnamed Activity' }}</h1>
                        <div class="flex items-center space-x-4 text-orange-100">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ isset($activity['start_date']) ? \Carbon\Carbon::parse($activity['start_date'])->format('l, d F Y H:i') : 'Date not available' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $activity['sport_type'] ?? 'Unknown Type' }}
                            </span>
                        </div>
                    </div>

                    <!-- GEAR SECTION - FIXED FOR ARRAY -->
                    <div class="bg-white/20 rounded-lg px-4 py-2 text-center">
                        <span class="text-sm opacity-90">Gear Used</span>
                        <p class="font-bold">
                            @if (isset($activity['gear']) && !empty($activity['gear']))
                                {{ $activity['gear']['nickname'] ?? ($activity['gear']['name'] ?? 'Not Specified') }}
                            @else
                                Not Specified
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- KEY STATS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Distance Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Distance</p>
                            <h3 class="text-3xl font-bold text-gray-800">
                                {{ isset($activity['distance']) ? number_format($activity['distance'] / 1000, 2) : '0' }}
                                <span class="text-sm font-normal text-gray-500">km</span>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ isset($activity['distance']) ? number_format($activity['distance'], 0) : '0' }}
                                meters</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Time Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Moving Time</p>
                            <h3 class="text-3xl font-bold text-gray-800">
                                @php
                                    $movingTime = $activity['moving_time'] ?? 0;
                                @endphp
                                {{ floor($movingTime / 60) }}<span class="text-sm font-normal text-gray-500">m
                                    {{ $movingTime % 60 }}s</span>
                            </h3>
                            @php
                                $elapsedTime = $activity['elapsed_time'] ?? 0;
                            @endphp
                            <p class="text-sm text-gray-500 mt-1">Elapsed: {{ floor($elapsedTime / 60) }}m
                                {{ $elapsedTime % 60 }}s</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pace/Speed Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Average Pace</p>
                            <h3 class="text-3xl font-bold text-gray-800">
                                @php
                                    $avgSpeed = $activity['average_speed'] ?? 0;
                                    if ($avgSpeed > 0) {
                                        $paceInSeconds = 1000 / ($avgSpeed * 60);
                                        $paceMinutes = floor($paceInSeconds);
                                        $paceSeconds = floor(($paceInSeconds - $paceMinutes) * 60);
                                        echo $paceMinutes . ':' . str_pad($paceSeconds, 2, '0', STR_PAD_LEFT);
                                    } else {
                                        echo '0:00';
                                    }
                                @endphp
                                <span class="text-sm font-normal text-gray-500">/km</span>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Max:
                                {{ isset($activity['max_speed']) ? number_format($activity['max_speed'] * 3.6, 1) : '0' }}
                                km/h</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Elevation Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Elevation Gain</p>
                            <h3 class="text-3xl font-bold text-gray-800">
                                {{ number_format($activity['total_elevation_gain'] ?? 0, 1) }} <span
                                    class="text-sm font-normal text-gray-500">m</span></h3>
                            <p class="text-sm text-gray-500 mt-1">High: {{ $activity['elev_high'] ?? 0 }}m | Low:
                                {{ $activity['elev_low'] ?? 0 }}m</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP ANALYSIS HERO -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Route Visualization Analysis</h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-blue-600 rounded-full mr-1"></span>
                            Start
                        </span>
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-red-600 rounded-full mr-1"></span>
                            End
                        </span>
                    </div>
                </div>
                <div id="map" style="height:450px" class="rounded-xl"></div>

                <!-- Coordinates Info -->
                <div class="grid grid-cols-2 gap-4 mt-4 text-sm text-gray-500">
                    <div>
                        <span class="font-medium text-gray-700">Start:</span>
                        {{ isset($activity['start_latlng'][0]) ? $activity['start_latlng'][0] : 'N/A' }},
                        {{ isset($activity['start_latlng'][1]) ? $activity['start_latlng'][1] : 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">End:</span>
                        {{ isset($activity['end_latlng'][0]) ? $activity['end_latlng'][0] : 'N/A' }},
                        {{ isset($activity['end_latlng'][1]) ? $activity['end_latlng'][1] : 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- ADVANCED STATS GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Performance Metrics -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Performance Metrics</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Average Cadence</span>
                                <span class="font-medium text-gray-800">{{ $activity['average_cadence'] ?? 0 }}
                                    spm</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ (($activity['average_cadence'] ?? 0) / 100) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Calories Burned</span>
                                <span class="font-medium text-gray-800">{{ $activity['calories'] ?? 0 }} kcal</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full"
                                    style="width: {{ (($activity['calories'] ?? 0) / 600) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Achievement Count</span>
                                <span
                                    class="font-medium text-gray-800">{{ $activity['achievement_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-500">Kudos Received</span>
                                <span class="font-medium text-gray-800">{{ $activity['kudos_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Splits Analysis -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-4">Splits Analysis (Metric)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-xs text-gray-500 border-b border-gray-100">
                                    <th class="text-left py-2">Split</th>
                                    <th class="text-left py-2">Distance</th>
                                    <th class="text-left py-2">Time</th>
                                    <th class="text-left py-2">Pace</th>
                                    <th class="text-left py-2">Elev Δ</th>
                                    <th class="text-left py-2">Speed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($activity['splits_metric'] ?? []) as $split)
                                    <tr class="border-b border-gray-50 text-sm">
                                        <td class="py-2 font-medium">Split {{ $split['split'] ?? 'N/A' }}</td>
                                        <td class="py-2">{{ number_format($split['distance'] ?? 0, 0) }}m</td>
                                        <td class="py-2">
                                            @php
                                                $movingTime = $split['moving_time'] ?? 0;
                                            @endphp
                                            {{ floor($movingTime / 60) }}:{{ str_pad($movingTime % 60, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-2">
                                            @php
                                                $avgSpeed = $split['average_speed'] ?? 0;
                                                if ($avgSpeed > 0) {
                                                    $paceInSeconds = 1000 / ($avgSpeed * 60);
                                                    $paceMinutes = floor($paceInSeconds);
                                                    $paceSeconds = floor(($paceInSeconds - $paceMinutes) * 60);
                                                    echo $paceMinutes .
                                                        ':' .
                                                        str_pad($paceSeconds, 2, '0', STR_PAD_LEFT);
                                                } else {
                                                    echo '0:00';
                                                }
                                            @endphp
                                        </td>
                                        <td
                                            class="py-2 {{ ($split['elevation_difference'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ ($split['elevation_difference'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($split['elevation_difference'] ?? 0, 1) }}m
                                        </td>
                                        <td class="py-2">
                                            {{ number_format(($split['average_speed'] ?? 0) * 3.6, 1) }} km/h</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-500">No split data
                                            available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BEST EFFORTS SECTION -->
            @if (isset($activity['best_efforts']) && count($activity['best_efforts']) > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Best Efforts</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        @foreach ($activity['best_efforts'] as $effort)
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <p class="text-sm text-gray-500 mb-1">{{ $effort['name'] ?? 'Unknown' }}</p>
                                <p class="text-xl font-bold text-gray-800">
                                    @php
                                        $elapsedTime = $effort['elapsed_time'] ?? 0;
                                    @endphp
                                    {{ floor($elapsedTime / 60) }}:{{ str_pad($elapsedTime % 60, 2, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ number_format($effort['distance'] ?? 0, 0) }}m</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ADDITIONAL INFO & GEAR -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Activity Details -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Activity Details</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Sport Type</dt>
                            <dd class="font-medium text-gray-800">
                                {{ $activity['sport_type'] ?? ($activity['type'] ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Device</dt>
                            <dd class="font-medium text-gray-800">{{ $activity['device_name'] ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Timezone</dt>
                            <dd class="font-medium text-gray-800">{{ $activity['timezone'] ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Visibility</dt>
                            <dd class="font-medium text-gray-800">{{ ucfirst($activity['visibility'] ?? 'private') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Trainer</dt>
                            <dd class="font-medium text-gray-800">
                                {{ isset($activity['trainer']) ? ($activity['trainer'] ? 'Yes' : 'No') : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Commute</dt>
                            <dd class="font-medium text-gray-800">
                                {{ isset($activity['commute']) ? ($activity['commute'] ? 'Yes' : 'No') : 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>


                <!-- Gear Information -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Gear Information</h3>

                    @if (isset($activity['gear']) && !empty($activity['gear']))
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $activity['gear']['name'] ?? 'Unknown Gear' }}
                                </p>
                                <p class="text-sm text-gray-500">Nickname:
                                    {{ $activity['gear']['nickname'] ?? 'N/A' }}</p>

                                @if (isset($activity['gear']['distance']))
                                    <p class="text-sm text-gray-500 mt-2">Total Distance:
                                        {{ number_format($activity['gear']['distance'] / 1000, 1) }} km</p>
                                @endif

                                @if (isset($activity['gear']['converted_distance']))
                                    <p class="text-sm text-gray-500 mt-2">Total Distance:
                                        {{ number_format($activity['gear']['converted_distance'], 1) }} km</p>
                                @endif

                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ isset($activity['gear']['retired']) && $activity['gear']['retired'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ isset($activity['gear']['retired']) && $activity['gear']['retired'] ? 'Retired' : 'Active' }}
                                    </span>
                                </div>

                                @if (isset($activity['gear']['id']))
                                    <p class="text-xs text-gray-400 mt-2">Gear ID: {{ $activity['gear']['id'] }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No gear information available for this activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MAP SCRIPT -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const coordinates = @json($coordinates ?? []);

        if (coordinates && coordinates.length > 0) {
            // Initialize map
            const map = L.map('map');

            // Decode polyline if needed (assuming coordinates are already decoded)
            const route = L.polyline(coordinates, {
                color: '#3b82f6',
                weight: 4,
                opacity: 0.8,
                lineJoin: 'round'
            }).addTo(map);

            // Add start and end markers
            if (coordinates.length > 0) {
                // Start marker
                L.marker(coordinates[0], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<div style="background-color: #3b82f6; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>',
                        iconSize: [12, 12],
                        popupAnchor: [0, -10]
                    })
                }).addTo(map).bindPopup('Start');

                // End marker
                L.marker(coordinates[coordinates.length - 1], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<div style="background-color: #ef4444; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>',
                        iconSize: [12, 12],
                        popupAnchor: [0, -10]
                    })
                }).addTo(map).bindPopup('Finish');
            }

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Fit bounds
            map.fitBounds(route.getBounds(), {
                padding: [50, 50]
            });
        } else {
            // Show message if no coordinates
            document.getElementById('map').innerHTML =
                '<div class="flex items-center justify-center h-full bg-gray-100 rounded-xl"><p class="text-gray-500">No route data available</p></div>';
        }
    </script>

    <style>
        .custom-div-icon {
            background: transparent;
            border: none;
        }
    </style>
</x-app-layout>
