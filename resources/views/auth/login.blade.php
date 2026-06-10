<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Sistem Presensi Murid</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gradient-to-br from-blue-600 to-purple-600 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-center">
                <h1 class="text-4xl font-bold text-white mb-2">📚 Presensi</h1>
                <p class="text-blue-100">Sistem Absensi Murid</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <form action="/" method="post" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" placeholder="nama@sekolah.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-700">Ingat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition duration-200 shadow-lg">
                        Masuk
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-600 text-sm">
                        Hubungi admin jika belum memiliki akun
                    </p>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center text-white">
            <p class="text-sm">Sistem Manajemen Presensi Sekolah © 2024</p>
        </div>
    </div>
</body>

</html>
