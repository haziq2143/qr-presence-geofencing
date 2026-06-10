<x-app-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Scan Absensi QR Code</h1>
            <p class="text-blue-100">Arahkan kamera ke QR code absensi</p>
        </div>

        <!-- Geolocation Status -->
        <div id="geoStatus" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-lg">
            <div class="flex items-center">
                <span class="text-yellow-500 mr-3">⏳</span>
                <div>
                    <p class="font-semibold text-yellow-800">Mendeteksi Lokasi...</p>
                    <p class="text-yellow-700 text-sm">Mendapatkan koordinat GPS Anda</p>
                </div>
            </div>
        </div>

        <!-- Camera Container -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div id="cameraContainer" class="relative w-full bg-black">
                <video id="qrVideo" class="w-full" style="max-height: 500px; display: block;"></video>
                <div id="scannerOverlay" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="border-4 border-blue-400 w-64 h-64 rounded-lg shadow-lg"
                        style="box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.3);"></div>
                </div>
            </div>
        </div>

        <!-- Scanner Status -->
        <div id="scanStatus" class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8 rounded-lg">
            <div class="flex items-center">
                <span class="text-blue-500 mr-3">📹</span>
                <div>
                    <p class="font-semibold text-blue-800">Scanner Aktif</p>
                    <p class="text-blue-700 text-sm">Arahkan QR code ke dalam kotak untuk di-scan</p>
                </div>
            </div>
        </div>

        <!-- Manual Entry Fallback -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <button type="button" id="toggleManualBtn" class="text-blue-600 hover:text-blue-800 font-semibold">
                ▼ Input Manual Kode Absensi
            </button>

            <div id="manualEntryForm" class="hidden mt-4 pt-4 border-t border-gray-200">
                <form action="/record" method="POST" class="space-y-4" id="manualForm">
                    @csrf
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                    <div>
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode
                            Absensi</label>
                        <input type="text" name="attendance_code" id="code" placeholder="Masukkan kode absensi"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        Kirim Absensi
                    </button>
                </form>
            </div>
        </div>

        <a href="/"
            class="inline-block px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
            Kembali
        </a>
    </div>

    <!-- Hidden form for auto-submission -->
    <form id="qrSubmitForm" action="/record" method="POST" style="display: none;">
        @csrf
        <input type="hidden" id="qrCode" name="attendance_code">
        <input type="hidden" id="formLatitude" name="latitude">
        <input type="hidden" id="formLongitude" name="longitude">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        // School coordinates
        const SCHOOL_LAT = -6.0462467;
        const SCHOOL_LNG = 106.0518361;
        const MAX_DISTANCE = 100;

        let studentLat = null;
        let studentLng = null;
        let cameraStream = null;
        let lastScannedCode = null;
        let scanDebounce = false;
        let isLocationSpoofed = false;
        let positionHistory = [];

        // Calculate distance
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Start camera
        async function startCamera() {
            try {
                const constraints = {
                    video: {
                        facingMode: 'environment',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    },
                    audio: false
                };

                cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
                const video = document.getElementById('qrVideo');
                video.srcObject = cameraStream;
                video.setAttribute('playsinline', true);
                video.play();

                scanQRCode();
            } catch (error) {
                showScanError('Tidak dapat mengakses kamera. Silakan gunakan input manual.');
            }
        }

        // Scan QR code from video
        function scanQRCode() {
            const video = document.getElementById('qrVideo');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            if (canvas.width === 0) {
                requestAnimationFrame(scanQRCode);
                return;
            }

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code && code.data && !scanDebounce) {
                const fullCode = code.data;
                if (fullCode !== lastScannedCode) {
                    lastScannedCode = fullCode;
                    scanDebounce = true;

                    handleScannedCode(fullCode);

                    setTimeout(() => {
                        scanDebounce = false;
                    }, 2000);
                }
            }

            requestAnimationFrame(scanQRCode);
        }

        // Handle scanned code
        async function handleScannedCode(code) {
            if (isLocationSpoofed) {
                showScanError('Absen Ditolak: Pemalsuan lokasi terdeteksi!');
                alert('Deteksi Kecurangan: Matikan Fake GPS / Mock Location untuk melakukan presensi.');
                return;
            }
            try {
                const response = await fetch(`/api/attendance/type/${code}`);
                const data = await response.json();

                if (data.type === 'online') {
                    showGeoOnline();
                    submitQRCode(code);
                } else {
                    if (!studentLat || !studentLng) {
                        showScanError('Lokasi belum terdeteksi. Tunggu sebentar...');
                        return;
                    }
                    const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, studentLat, studentLng);
                    if (distance > MAX_DISTANCE) {
                        showScanError(`Anda ${Math.round(distance - MAX_DISTANCE)}m di luar radius sekolah`);
                        return;
                    }
                    submitQRCode(code);
                }
            } catch (err) {
                // Fallback
                if (!studentLat || !studentLng) {
                    showScanError('Lokasi belum terdeteksi. Tunggu sebentar...');
                    return;
                }
                const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, studentLat, studentLng);
                if (distance > MAX_DISTANCE) {
                    showScanError(`Anda ${Math.round(distance - MAX_DISTANCE)}m di luar radius sekolah`);
                    return;
                }
                submitQRCode(code);
            }
        }

        // Submit QR code
        function submitQRCode(code) {
            if (isLocationSpoofed) {
                alert('Absen Ditolak: Pemalsuan lokasi terdeteksi!');
                return;
            }
            document.getElementById('qrCode').value = code;
            document.getElementById('formLatitude').value = studentLat;
            document.getElementById('formLongitude').value = studentLng;

            // Stop camera before submitting
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }

            document.getElementById('qrSubmitForm').submit();
        }

        // Detect Fake GPS / Mock Location
        function detectSpoofing(position) {
            // 1. Check if browser geolocation APIs have been overridden/tampered
            try {
                const currentStr = navigator.geolocation.getCurrentPosition.toString();
                const watchStr = navigator.geolocation.watchPosition.toString();
                if (!currentStr.includes('[native code]') || !watchStr.includes('[native code]')) {
                    return { spoofed: true, reason: 'Manipulasi Geolocation API terdeteksi (Ekstensi/Skrip Pemalsu)' };
                }
            } catch (e) {}

            // 2. Check navigator.webdriver (automated browsers / emulators)
            if (navigator.webdriver) {
                return { spoofed: true, reason: 'Lingkungan browser otomatis/emulator terdeteksi' };
            }

            // 3. Check for invalid or suspicious accuracy
            const accuracy = position.coords.accuracy;
            if (accuracy <= 0) {
                return { spoofed: true, reason: 'Akurasi GPS tidak wajar (0 meter)' };
            }

            // 4. Check for standard mock flags
            if (position.mocked || (position.coords && position.coords.mocked)) {
                return { spoofed: true, reason: 'Sinyal lokasi palsu terdeteksi oleh sistem' };
            }

            return { spoofed: false };
        }

        // Get geolocation
        function getGeolocation() {
            if (!navigator.geolocation) {
                showGeoError('Geolocation tidak didukung');
                return;
            }

            navigator.geolocation.watchPosition(
                function(position) {
                    // Check for spoofing
                    const spoofCheck = detectSpoofing(position);
                    if (spoofCheck.spoofed) {
                        isLocationSpoofed = true;
                        showGeoSpoofed(spoofCheck.reason);
                        return;
                    }

                    // Static coordinate checks for zero variance (only check if we get multiple updates)
                    const currentCoords = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    
                    positionHistory.push(currentCoords);
                    if (positionHistory.length > 5) {
                        positionHistory.shift();
                    }

                    if (positionHistory.length >= 3) {
                        const allIdentical = positionHistory.every(pos => 
                            pos.lat === currentCoords.lat && 
                            pos.lng === currentCoords.lng && 
                            pos.accuracy === currentCoords.accuracy
                        );
                        
                        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                        if (allIdentical && isMobile) {
                            isLocationSpoofed = true;
                            showGeoSpoofed('Deteksi koordinat statis tanpa fluktuasi (indikasi Fake GPS)');
                            return;
                        }
                    }

                    isLocationSpoofed = false;
                    studentLat = position.coords.latitude;
                    studentLng = position.coords.longitude;
                    const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, studentLat, studentLng);

                    if (distance <= MAX_DISTANCE) {
                        showGeoSuccess(distance);
                    } else {
                        showGeoWarning(distance);
                    }
                },
                function(error) {
                    let msg = 'Gagal mendapat lokasi';
                    if (error.code === 1) msg = 'Izin lokasi ditolak';
                    showGeoError(msg);
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
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
        }

        function showGeoSuccess(distance) {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-green-500 mr-3 text-xl">✓</span>
                    <div>
                        <p class="font-semibold text-green-800">Lokasi Terverifikasi</p>
                        <p class="text-green-700 text-sm">${Math.round(distance)}m dari sekolah</p>
                    </div>
                </div>
            `;
            status.className = 'bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded-lg';
        }

        function showGeoWarning(distance) {
            const status = document.getElementById('geoStatus');
            const away = Math.round(distance - MAX_DISTANCE);
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-yellow-500 mr-3 text-xl">⚠️</span>
                    <div>
                        <p class="font-semibold text-yellow-800">Peringatan Lokasi</p>
                        <p class="text-yellow-700 text-sm">${away}m di luar radius sekolah</p>
                    </div>
                </div>
            `;
            status.className = 'bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-lg';
        }

        function showGeoError(msg) {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-red-500 mr-3 text-xl">✗</span>
                    <div>
                        <p class="font-semibold text-red-800">Lokasi Gagal</p>
                        <p class="text-red-700 text-sm">${msg}</p>
                    </div>
                </div>
            `;
            status.className = 'bg-red-50 border-l-4 border-red-400 p-4 mb-8 rounded-lg';
        }

        function showGeoSpoofed(reason) {
            const status = document.getElementById('geoStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-red-600 mr-3 text-2xl">🚫</span>
                    <div>
                        <p class="font-bold text-red-900">Pemalsuan Lokasi Terdeteksi!</p>
                        <p class="text-red-700 text-sm">${reason}.</p>
                        <p class="text-red-600 text-xs mt-1 font-semibold">Harap nonaktifkan Fake GPS / Mock Location / Ekstensi pemalsu lokasi untuk absensi.</p>
                    </div>
                </div>
            `;
            status.className = 'bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-lg animate-pulse';
        }

        function showScanError(msg) {
            const status = document.getElementById('scanStatus');
            status.innerHTML = `
                <div class="flex items-center">
                    <span class="text-red-500 mr-3 text-xl">❌</span>
                    <div>
                        <p class="font-semibold text-red-800">${msg}</p>
                    </div>
                </div>
            `;
            status.className = 'bg-red-50 border-l-4 border-red-400 p-4 mb-8 rounded-lg';
        }

        // Toggle manual entry
        document.getElementById('toggleManualBtn').addEventListener('click', function() {
            const form = document.getElementById('manualEntryForm');
            form.classList.toggle('hidden');
            this.textContent = form.classList.contains('hidden') ? '▼ Input Manual Kode Absensi' :
                '▲ Tutup Input Manual';
        });

        // Manual form submit check
        document.getElementById('manualForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (isLocationSpoofed) {
                alert('Absen Ditolak: Pemalsuan lokasi terdeteksi! Harap nonaktifkan Fake GPS / Mock Location.');
                return;
            }
            const codeInput = document.getElementById('code').value;
            if (!codeInput) {
                alert('Kode absensi harus diisi');
                return;
            }

            // Fill coordinates if available
            document.getElementById('latitude').value = studentLat;
            document.getElementById('longitude').value = studentLng;

            try {
                const response = await fetch(`/api/attendance/type/${codeInput}`);
                const data = await response.json();
                
                if (data.type === 'online') {
                    showGeoOnline();
                    this.submit();
                } else {
                    if (!studentLat || !studentLng) {
                        alert('Lokasi belum terdeteksi. Silakan aktifkan GPS Anda.');
                        return;
                    }
                    const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, studentLat, studentLng);
                    if (distance > MAX_DISTANCE) {
                        alert(`Anda berada ${Math.round(distance - MAX_DISTANCE)} meter di luar radius sekolah.`);
                        return;
                    }
                    this.submit();
                }
            } catch (err) {
                this.submit();
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            getGeolocation();
            startCamera();
        });

        // Stop camera when leaving
        window.addEventListener('beforeunload', function() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</x-app-layout>
