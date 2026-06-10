<x-app-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100">Lihat riwayat absensi Anda di bawah ini</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Kelas Saya</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ optional(Auth::user()->classStudent)->class ?? 'N/A' }}
                </h3>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Email</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ Auth::user()->email }}</h3>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm font-semibold mb-2">Tanggal Hari Ini</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ \Carbon\Carbon::now()->format('d M Y') }}</h3>
            </div>
        </div>

        <!-- Attendance History -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Riwayat Absensi</h2>
        <div class="relative overflow-x-auto rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-white uppercase bg-blue-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Status Absensi</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $index => $attendance)
                        <tr
                            class="{{ $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} border-b hover:bg-blue-100 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $attendance->attendance->subject->subject }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::parse($attendance->attendance->attendance_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($attendance->description == null)
                                    <span
                                        class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                        ⏳ Belum Absen
                                    </span>
                                @else
                                    <span
                                        class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                        ✓ {{ $attendance->description }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $attendance->attendance->class->class }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">Belum ada data absensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
