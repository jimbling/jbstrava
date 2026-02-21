<x-app-layout>

<div class="py-10 bg-gray-50 min-h-screen">

<div class="max-w-7xl mx-auto px-6 space-y-8">

<!-- HEADER SUMMARY -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

<h1 class="text-2xl font-bold text-gray-800">
Activity Analysis
</h1>

<p class="text-gray-500 mt-1">
{{ $activity->name }}
</p>

</div>


<!-- KPI ANALYTICS GRID -->
<div class="grid md:grid-cols-4 gap-6">

<div class="bg-white p-6 rounded-2xl shadow-sm">

<p class="text-sm text-gray-500">Distance</p>

<h2 class="text-2xl font-bold text-gray-800">
{{ number_format($activity->distance/1000,2) }} km
</h2>

</div>


<div class="bg-white p-6 rounded-2xl shadow-sm">

<p class="text-sm text-gray-500">Moving Time</p>

<h2 class="text-2xl font-bold text-gray-800">
{{ gmdate("H:i:s",$activity->moving_time) }}
</h2>

</div>


<div class="bg-white p-6 rounded-2xl shadow-sm">

<p class="text-sm text-gray-500">Elevation Gain</p>

<h2 class="text-2xl font-bold text-gray-800">
{{ number_format($activity->total_elevation_gain,1) }} m
</h2>

</div>


<div class="bg-white p-6 rounded-2xl shadow-sm">

<p class="text-sm text-gray-500">Average Speed</p>

<h2 class="text-2xl font-bold text-gray-800">
{{ number_format($activity->average_speed,2) }} m/s
</h2>

</div>

</div>


<!-- MAP ANALYSIS VISUALIZATION -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">

<h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">

<span class="text-[#fc4c02]">●</span>
Route Visualization

</h3>

<div id="map" style="height:420px" class="rounded-xl"></div>

</div>


<!-- ACHIEVEMENT INSIGHT -->
@if($activity->achievement_flag || $activity->is_personal_record)

<div class="bg-white p-6 rounded-2xl shadow-sm">

<h3 class="font-semibold text-gray-800 mb-4">
Performance Insight
</h3>

<div class="flex gap-4 flex-wrap">

@if($activity->achievement_flag)
<span class="px-4 py-2 bg-orange-100 text-[#fc4c02] rounded-xl text-sm font-semibold">
🔥 Achievement Activity
</span>
@endif

@if($activity->is_personal_record)
<span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold">
⭐ Personal Record
</span>
@endif

</div>

</div>

@endif


<!-- RAW DATA ANALYSIS VIEW -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">

<details class="cursor-pointer">

<summary class="font-semibold text-[#fc4c02]">
Show Raw Activity Analysis JSON
</summary>

<pre class="mt-4 bg-gray-50 p-4 rounded-xl text-xs overflow-auto">
{{ json_encode($activity->raw_data, JSON_PRETTY_PRINT) }}
</pre>

</details>

</div>


</div>
</div>


<!-- MAP SCRIPT -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

const coordinates = @json($coordinates);

if (coordinates.length > 0) {

const map = L.map('map');

const route = L.polyline(coordinates, {
weight: 4
}).addTo(map);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
.addTo(map);

map.fitBounds(route.getBounds());

}

</script>

</x-app-layout>
