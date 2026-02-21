<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto px-6">



            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">


                <h2 class="text-2xl font-bold text-gray-800">
                    Daftar Aktivitas Strava
                </h2>

                <form action="{{ route('strava.sync') }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="px-5 py-2 bg-[#fc4c02] hover:bg-[#e64500]
               text-white rounded-xl shadow-md
               transition flex items-center gap-2">

                        <span>Sinkronisasi Aktivitas</span>
                    </button>
                </form>


            </div>


            <!-- Activity Grid -->
            <div class="grid md:grid-cols-3 gap-6">

                @foreach ($activities as $activity)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

                        <h3 class="font-semibold text-gray-800 hover:text-[#fc4c02] transition">
                            <a href="{{ route('strava.activity.show', $activity->id) }}">
                                {{ $activity->name }}
                            </a>
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ $activity->sport_type }}
                        </p>

                        <div class="mt-4 space-y-1 text-sm">

                            <p>Distance : {{ number_format($activity->distance / 1000, 2) }} km</p>

                            <p>Time :
                                {{ gmdate('H:i:s', $activity->moving_time) }}
                            </p>

                            <p>Elevation :
                                {{ number_format($activity->total_elevation_gain, 1) }} m
                            </p>

                        </div>

                    </div>
                @endforeach

            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>


        </div>
    </div>
</x-app-layout>
