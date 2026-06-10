<x-admin-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Daftar Mata Pelajaran</h1>
            <p class="text-green-100">Total mata pelajaran: <span class="font-bold">{{ $subjects->count() }}</span></p>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end mb-6">
            <a href="/subjects/create"
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                ➕ Tambah Mata Pelajaran
            </a>
        </div>

        <!-- Table -->
        <div class="relative overflow-x-auto rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-white uppercase bg-green-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Guru Pengajar</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $index => $subject)
                        <tr
                            class="{{ $index % 2 == 0 ? 'bg-green-50' : 'bg-white' }} border-b hover:bg-green-100 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $subject->subject }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $subject->teacher->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="/subjects/{{ $subject->id }}/edit"
                                    class="inline-block px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md mr-2 transition duration-200">
                                    Edit
                                </a>
                                <form method="POST" action="/subjects/{{ $subject->id }}" class="inline-block">
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
                                <p class="text-lg">Tidak ada data mata pelajaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
