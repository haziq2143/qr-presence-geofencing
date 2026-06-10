<x-app-layout>
    <div class="w-full px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Absensi Tanggal
                {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</h1>
            <p class="text-blue-50">Detail sesi absensi</p>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Subject Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Mata Pelajaran</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $attendance->subject->subject }}</h3>
            </div>

            <!-- Teacher Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Guru Pengajar</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $attendance->subject->teacher->name }}</h3>
            </div>

            <!-- Class Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Kelas</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $attendance->class->class }}</h3>
            </div>
        </div>

        <!-- QR Code Section with Auto Refresh -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Kode Absensi QR (Auto-Refresh)</h2>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Berlaku dalam</p>
                    <div class="text-3xl font-bold text-blue-600" id="countdown">30</div>
                    <p class="text-xs text-gray-500">detik</p>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-gray-100 p-6 rounded-lg mb-4" id="qrContainer">
                    {{ $qr }}
                </div>
                <p class="text-lg font-mono font-bold text-gray-700" id="codeDisplay">{{ $attendance->attendance_code }}
                </p>
                <p class="text-sm text-gray-500 mt-2">Kode QR berubah setiap 30 detik untuk mencegah penyalahgunaan</p>
            </div>
        </div>

        <!-- Back Button -->
        <a href="/attendances"
            class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
            ← Kembali
        </a>
    </div>

    <script>
        const attendanceId = {{ $attendance->id }};
        let countdown = 30;

        function refreshQR() {
            // Tambahkan parameter waktu (cache-buster) agar browser tidak mengambil dari cache!
            fetch(`/api/attendance/${attendanceId}/qr?t=${new Date().getTime()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.qr) {
                        document.getElementById('qrContainer').innerHTML = data.qr;
                    }
                    if (data.code) {
                        document.getElementById('codeDisplay').textContent = data.code;
                    }
                })
                .catch(error => console.error('Error refreshing QR:', error));
        }

        // Gunakan satu timer saja untuk menghitung mundur and merefresh QR
        setInterval(() => {
            countdown--;
            
            if (countdown <= 0) {
                countdown = 30;
                refreshQR();
            }
            
            document.getElementById('countdown').textContent = countdown;
        }, 1000);
    </script>
</x-app-layout>
