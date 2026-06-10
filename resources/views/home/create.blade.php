<x-app-layout>
    <div class="w-full max-w-4xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Input Absensi Siswa</h1>
            <p class="text-blue-100">Pilih status kehadiran untuk setiap siswa</p>
        </div>

        <!-- Geolocation Status Alert -->
        <div id="geoStatus" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-lg">
            <div class="flex items-center">
                <span class="text-yellow-500 mr-3">⏳</span>
                <div>
                    <p class="font-semibold text-yellow-800">Mendeteksi Lokasi...</p>
                    <p class="text-yellow-700 text-sm">Aplikasi memerlukan akses lokasi Anda untuk verifikasi kehadiran
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/home/store/{{ $id }}" method="post" class="space-y-6" id="attendanceForm">
                @csrf

                <!-- Hidden Geolocation Fields -->
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
                <input type="hidden" id="distance" name="distance">

                <!-- Students List -->
                <div class="space-y-4">
                    @forelse ($users as $user)
                        <div class="bg-blue-50/50 rounded-lg p-6 border border-blue-200">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2">{{ $user->name }}</label>
                                    <input type="number" value="{{ $user->id }}" class="hidden" name="students[]"
                                        readonly>
                                </div>
                                <div class="flex-1 md:max-w-xs">
                                    <select name="descriptions[]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Hadir">✓ Hadir</option>
                                        <option value="Izin">📝 Izin</option>
                                        <option value="Sakit">🤒 Sakit</option>
                                        <option value="Alpha">❌ Alpha</option>
                                    </select>
                                    @error('descriptions[]')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <p class="text-yellow-800 text-center">Tidak ada siswa dalam kelas ini</p>
                        </div>
                    @endforelse
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed"
                        id="submitBtn">
                        Simpan Absensi
                    </button>
                    <a href="/"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 shadow-md text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const isOnline = {{ $attendance->type === 'online' ? 'true' : 'false' }};

        // School coordinates
        const SCHOOL_LAT = -6.0462467;
        const SCHOOL_LNG = 106.0518361;
        const MAX_DISTANCE = 100; // meters

        // Calculate distance using Haversine formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Get geolocation
        function getGeolocation() {
            if (isOnline) {
                showGeoOnline();
            }

            if (!navigator.geolocation) {
                if (!isOnline) {
                    showGeoError('Geolocation tidak didukung browser Anda');
                }
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, lat, lng);

                    // Store values
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('distance').value = distance;

                    // Check if within radius
                    if (isOnline) {
                        showGeoOnline();
                    } else if (distance <= MAX_DISTANCE) {
                        showGeoSuccess(distance);
                    } else {
                        showGeoError(
                            `Anda berada ${Math.round(distance - MAX_DISTANCE)} meter di luar radius sekolah`
                        );
                    }
                },
                function(error) {
                    if (isOnline) {
                        showGeoOnline();
                        return;
                    }
                    let errorMsg = 'Gagal mendapatkan lokasi';
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMsg = 'Izin akses lokasi ditolak. Silakan aktifkan lokasi di pengaturan browser.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errorMsg = 'Lokasi tidak tersedia saat ini';
                    }
                    showGeoError(errorMsg);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function showGeoOnline() {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-green-500 mr-3 text-xl">✓</span>
                    <div>
                        <p class="font-semibold text-green-800">Kelas Daring (Online) - Bebas Jarak</p>
                        <p class="text-green-700 text-sm">Tidak ada batasan lokasi untuk kelas daring ini.</p>
                    </div>
                </div>
            `;
            status.className = 'bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded-lg';
            document.getElementById('submitBtn').disabled = false;
        }

        function showGeoSuccess(distance) {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-green-500 mr-3 text-xl">✓</span>
                    <div>
                        <p class="font-semibold text-green-800">Lokasi Terverifikasi</p>
                        <p class="text-green-700 text-sm">Anda berada di dalam radius sekolah (${Math.round(distance)} meter)</p>
                    </div>
                </div>
            `;
            status.className = 'bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded-lg';
            document.getElementById('submitBtn').disabled = false;
        }

        function showGeoError(message) {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-red-500 mr-3 text-xl">✗</span>
                    <div>
                        <p class="font-semibold text-red-800">Lokasi Tidak Terverifikasi</p>
                        <p class="text-red-700 text-sm">${message}</p>
                    </div>
                </div>
            `;
            status.className = 'bg-red-50 border-l-4 border-red-400 p-4 mb-8 rounded-lg';
            document.getElementById('submitBtn').disabled = true;
        }

        // Form validation
        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            if (isOnline) {
                // Set placeholder values if coordinate detection failed but class is online
                if (!document.getElementById('latitude').value) {
                    document.getElementById('latitude').value = 0;
                    document.getElementById('longitude').value = 0;
                    document.getElementById('distance').value = 0;
                }
                return;
            }

            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const distance = parseFloat(document.getElementById('distance').value);

            if (!latitude || !longitude) {
                e.preventDefault();
                alert('Lokasi belum terdeteksi. Silakan refresh halaman.');
                return;
            }

            if (distance > MAX_DISTANCE) {
                e.preventDefault();
                alert(
                    `Anda berada ${Math.round(distance - MAX_DISTANCE)} meter di luar radius sekolah. Harap masuk ke area sekolah.`
                );
                return;
            }
        });

        // Get geolocation on page load
        document.addEventListener('DOMContentLoaded', getGeolocation);
    </script>
</x-app-layout>
