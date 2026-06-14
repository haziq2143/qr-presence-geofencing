<x-admin-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Edit Data Kelas</h1>
            <p class="text-purple-100">Perbarui informasi kelas {{ $class->class }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/classes/{{ $class->id }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Class Name -->
                <div>
                    <label for="class" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="class" id="class" placeholder="Contoh: X-A, XI-B"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        value="{{ old('class', $class->class) }}">
                    @error('class')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Homeroom Teacher -->
                <div>
                    <label for="teacher_id" class="block text-sm font-semibold text-gray-700 mb-2">Wali Kelas</label>
                    <select name="teacher_id" id="teacher_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition duration-200 shadow-md">
                        Perbarui Kelas
                    </button>
                    <a href="/classes"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 shadow-md text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
