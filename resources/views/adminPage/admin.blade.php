<x-admin-layout>
    <div class="w-full px-4 md:px-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Dashboard Admin</h1>
            <p class="text-indigo-100">Selamat datang di panel administrasi</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold">Total Siswa</p>
                        <h3 class="text-4xl font-bold mt-2">{{ $studentCount ?? 0 }}</h3>
                    </div>
                    <div class="text-5xl opacity-20">👥</div>
                </div>
            </div>

            <!-- Total Teachers -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-semibold">Total Guru</p>
                        <h3 class="text-4xl font-bold mt-2">{{ $teacherCount ?? 0 }}</h3>
                    </div>
                    <div class="text-5xl opacity-20">👨‍🏫</div>
                </div>
            </div>

            <!-- Total Classes -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-semibold">Total Kelas</p>
                        <h3 class="text-4xl font-bold mt-2">{{ $classCount ?? 0 }}</h3>
                    </div>
                    <div class="text-5xl opacity-20">🏫</div>
                </div>
            </div>

            <!-- Total Subjects -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-semibold">Total Pelajaran</p>
                        <h3 class="text-4xl font-bold mt-2">{{ $subjectCount ?? 0 }}</h3>
                    </div>
                    <div class="text-5xl opacity-20">📚</div>
                </div>
            </div>
        </div>

        <!-- Quick Access Links -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Akses Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="/students"
                    class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition border-l-4 border-blue-500">
                    <p class="font-semibold text-gray-900">👥 Kelola Siswa</p>
                    <p class="text-sm text-gray-600 mt-1">Lihat dan kelola data siswa</p>
                </a>
                <a href="/teachers"
                    class="p-4 bg-green-50 hover:bg-green-100 rounded-lg transition border-l-4 border-green-500">
                    <p class="font-semibold text-gray-900">👨‍🏫 Kelola Guru</p>
                    <p class="text-sm text-gray-600 mt-1">Lihat dan kelola data guru</p>
                </a>
                <a href="/classes"
                    class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition border-l-4 border-purple-500">
                    <p class="font-semibold text-gray-900">🏫 Kelola Kelas</p>
                    <p class="text-sm text-gray-600 mt-1">Lihat dan kelola data kelas</p>
                </a>
                <a href="/subjects"
                    class="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition border-l-4 border-orange-500">
                    <p class="font-semibold text-gray-900">📚 Kelola Pelajaran</p>
                    <p class="text-sm text-gray-600 mt-1">Lihat dan kelola mata pelajaran</p>
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
