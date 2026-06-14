<x-admin-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Edit Data Siswa</h1>
            <p class="text-blue-100">Perbarui informasi siswa {{ $student->name }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/students/{{ $student->id }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" placeholder="Masukkan nama siswa"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        value="{{ old('name', $student->name) }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" placeholder="contoh@sekolah.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        value="{{ old('email', $student->email) }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class Field -->
                <div>
                    <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select name="class_id" id="class_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('class_id', $student->class_id) == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->class }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-200 shadow-md">
                        Perbarui Siswa
                    </button>
                    <a href="/students"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 shadow-md text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
