<x-admin-layout>
    <div class="w-full max-w-2xl mx-auto px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Upload Data Siswa</h1>
            <p class="text-blue-100">Unggah file Excel/CSV untuk menambahkan banyak siswa sekaligus</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="font-semibold text-blue-900 mb-3">Format File yang Diterima:</h3>
            <ul class="text-blue-800 space-y-2 text-sm">
                <li>✓ Format: Excel (.xls, .xlsx) atau CSV (.csv)</li>
                <li>✓ Kolom yang diperlukan: No, Nama, Email, Kelas</li>
                <li>✓ Pastikan kelas sudah ada di sistem sebelum upload</li>
            </ul>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/students/import" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- File Input -->
                <div>
                    <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">Pilih File</label>
                    <div class="relative">
                        <input type="file" name="file" id="file" accept=".csv,.xls,.xlsx"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            required>
                    </div>
                    @error('file')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-200 shadow-md">
                        Upload File
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
