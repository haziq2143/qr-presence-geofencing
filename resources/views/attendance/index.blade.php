<x-app-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Manajemen Absensi</h1>
            <p class="text-blue-50">Kelola data absensi siswa dengan mudah</p>
        </div>

        <!-- Button -->
        <div class="flex justify-end mb-6">
            <a href="/attendances/create"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                + Tambah Absensi
            </a>
        </div>

        <!-- Table -->
        <div class="relative overflow-x-auto rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-white uppercase bg-blue-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            Pelajaran
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            Kelas
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $index => $attendance)
                        <tr
                            class="{{ $index % 2 == 0 ? 'bg-blue-50/50' : 'bg-white' }} border-b hover:bg-blue-100/30 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $attendance->subject->subject }}
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $attendance->class->class }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md mr-2 transition duration-200"
                                    href="/attendances/{{ $attendance->id }}">Detail</a>
                                <a href="/home/create/{{ $attendance->id }}"
                                    class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition duration-200">Absen</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">Tidak ada data absensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if (isset($attendances) && method_exists($attendances, 'links'))
            <div class="py-6 text-blue-600">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
