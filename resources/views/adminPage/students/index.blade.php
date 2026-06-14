<x-admin-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Daftar Siswa</h1>
            <p class="text-blue-100">Total siswa: <span class="font-bold">{{ $students->count() }}</span></p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mb-6 justify-end">
            <a href="/exporth"
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                📊 Export Excel
            </a>
            <a href="/students/create"
                class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                ➕ Tambah Siswa
            </a>
        </div>

        <!-- Table -->
        <div class="relative overflow-x-auto rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-white uppercase bg-blue-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Nama</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Email</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Kelas</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Password</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $student)
                        <tr
                            class="{{ $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} border-b hover:bg-blue-100 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $student->email }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ optional($student->classStudent)->class ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs font-mono">
                                    {{ $student->plain_password ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <a href="/students/{{ $student->id }}/edit" class="inline-block px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow text-sm transition duration-150 mr-1">
                                    Edit
                                </a>
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded shadow text-sm transition duration-150">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">Tidak ada data siswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($students instanceof \Illuminate\Pagination\Paginator)
            <div class="py-6 text-blue-600">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
