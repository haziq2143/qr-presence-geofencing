<x-admin-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Edit Data Guru</h1>
            <p class="text-red-100">Perbarui informasi guru {{ $teacher->name }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/teachers/{{ $teacher->id }}" method="post" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" placeholder="Masukkan nama guru"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                        value="{{ old('name', $teacher->name) }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" placeholder="contoh@sekolah.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                        value="{{ old('email', $teacher->email) }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru
                        (Opsional)</label>
                    <input type="password" name="password" id="password"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition duration-200 shadow-md">
                        Perbarui Guru
                    </button>
                    <a href="/teachers"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 shadow-md text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
