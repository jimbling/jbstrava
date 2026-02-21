<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto px-6">

            <!-- Flash Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Sync Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

                <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Sinkronisasi Data Aktivitas
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Platform analitik aktivitas olahraga
                        </p>

                        <div class="mt-2 text-sm text-gray-600">
                            Terakhir sinkronisasi:
                            <span class="font-medium text-gray-800">
                                {{ $lastSync ? $lastSync->diffForHumans() : 'Belum pernah sinkronisasi' }}
                            </span>
                        </div>
                    </div>

                    <!-- Sync Button AJAX -->
                    <button id="syncBtn" onclick="syncActivity()"
                        class="px-6 py-3 bg-[#fc4c02] hover:bg-[#e64500]
                        text-white rounded-xl shadow-md transition
                        flex items-center gap-2">

                        <span id="syncText">Sinkronisasi Sekarang</span>

                        <svg id="syncSpinner" class="hidden animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8H4z"></path>
                        </svg>

                    </button>

                </div>

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
                            <p>Time : {{ gmdate('H:i:s', $activity->moving_time) }}</p>
                            <p>Elevation : {{ number_format($activity->total_elevation_gain, 1) }} m</p>
                        </div>

                    </div>
                @endforeach

            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>

        </div>
    </div>

    <script>
        async function syncActivity() {

            const btn = document.getElementById('syncBtn');
            const spinner = document.getElementById('syncSpinner');
            const text = document.getElementById('syncText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            text.innerText = 'Sinkronisasi sedang berjalan...';

            try {

                const response = await fetch("{{ route('strava.sync') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                });

                const data = await response.json();

                if (data.status === 'error') {
                    toastr.error(data.message || 'Gagal memulai sinkronisasi');
                } else {
                    toastr.success('Sinkronisasi sedang diproses di background');
                }

                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (e) {
                toastr.error('Terjadi kesalahan saat sync');
            } finally {

                btn.disabled = false;
                spinner.classList.add('hidden');
                text.innerText = 'Sinkronisasi Sekarang';

            }
        }
    </script>

</x-app-layout>
