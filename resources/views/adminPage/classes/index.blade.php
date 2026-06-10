<x-admin-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Daftar Kelas</h1>
            <p class="text-purple-100">Total kelas: <span class="font-bold">{{ $classes->count() }}</span></p>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end mb-6">
            <a href="/classes/create"
                class="px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                ➕ Tambah Kelas
            </a>
        </div>

        <!-- Table -->
        <div class="relative overflow-x-auto rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-white uppercase bg-purple-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Nama Kelas</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Wali Kelas</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $index => $class)
                        <tr
                            class="{{ $index % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} border-b hover:bg-purple-100 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $class->class }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="bg-purple-200 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $class->teacher->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="/classes/{{ $class->id }}/edit"
                                    class="inline-block px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md mr-2 transition duration-200">
                                    Edit
                                </a>
                                <form method="POST" action="/classes/{{ $class->id }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition duration-200"
                                        onclick="return confirm('Apakah Anda yakin?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">Tidak ada data kelas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
