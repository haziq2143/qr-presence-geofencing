<x-app-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Buat Absensi Baru</h1>
            <p class="text-blue-50">Lengkapi form untuk membuat sesi absensi baru</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/attendances" method="POST" class="space-y-6">
                @csrf

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                    <select name="subject_id" id="subject"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select name="class_id" id="class"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="attendance_date" id="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        value="{{ old('attendance_date') }}">
                    @error('attendance_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class Type -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Kelas</label>
                    <select name="type" id="type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="offline" {{ old('type', 'offline') == 'offline' ? 'selected' : '' }}>Luring (Offline)</option>
                        <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>Daring (Online)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2" id="type-help-text">
                        Kelas Offline: Siswa harus berada di dalam radius sekolah untuk melakukan absensi.
                    </p>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-md">
                        Buat Absensi
                    </button>
                    <a href="/attendances"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 shadow-md text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const helpText = document.getElementById('type-help-text');

            function updateHelpText() {
                if (typeSelect.value === 'online') {
                    helpText.innerHTML = '<span class="text-blue-600 font-semibold">⚠️ Kelas Daring (Online): Fitur batas radius jarak dinonaktifkan.</span> Siswa dapat mengisi absensi dari mana saja.';
                } else {
                    helpText.textContent = 'Kelas Luring (Offline): Siswa harus berada di dalam radius sekolah untuk melakukan absensi.';
                }
            }

            typeSelect.addEventListener('change', updateHelpText);
            updateHelpText();
        });
    </script>
</x-app-layout>
