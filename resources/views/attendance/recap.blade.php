<x-app-layout>
    <div class="w-full px-4 md:px-0 py-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Rekap Absensi Siswa</h1>
            <p class="text-blue-50">Filter dan unduh rekap absensi per mata pelajaran dan kelas</p>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border border-blue-100">
            <form action="/attendances/recap" method="get" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <!-- Class Filter -->
                <div>
                    <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select id="class_id" name="class_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Filter -->
                <div>
                    <label for="subject_id" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                    <select id="subject_id" name="subject_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach ($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                                {{ $subj->subject }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Period Type Filter -->
                <div>
                    <label for="period_type" class="block text-sm font-semibold text-gray-700 mb-2">Periode Rekap</label>
                    <select id="period_type" name="period_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="weekly" {{ request('period_type', 'weekly') == 'weekly' ? 'selected' : '' }}>Mingguan (Per Minggu)</option>
                        <option value="monthly" {{ request('period_type') == 'monthly' ? 'selected' : '' }}>Bulanan (Per Bulan)</option>
                        <option value="semesterly" {{ request('period_type') == 'semesterly' ? 'selected' : '' }}>Semester (Per Semester)</option>
                    </select>
                </div>

                <!-- Period Inputs -->
                <div class="md:col-span-1">
                    <!-- Weekly Input -->
                    <div id="weekly_input_container" class="period-input">
                        <label for="week" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Minggu</label>
                        <input type="week" id="week" name="week" value="{{ request('week', date('Y-\WW')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Monthly Input -->
                    <div id="monthly_input_container" class="period-input hidden">
                        <label for="month" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Bulan</label>
                        <input type="month" id="month" name="month" value="{{ request('month', date('Y-m')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Semesterly Inputs -->
                    <div id="semesterly_input_container" class="period-input hidden grid grid-cols-2 gap-2">
                        <div>
                            <label for="semester_year" class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                            <select id="semester_year" name="semester_year"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ request('semester_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="semester" class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
                            <select id="semester" name="semester"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="ganjil" {{ request('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul-Des)</option>
                                <option value="genap" {{ request('semester') == 'genap' ? 'selected' : '' }}>Genap (Jan-Jun)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="md:col-span-4 flex justify-end gap-3 mt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                        🔍 Tampilkan Preview
                    </button>
                    @if (isset($matrix) && count($matrix) > 0)
                        <a href="/attendances/recap/download?{{ http_build_query(request()->all()) }}"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                            <span>📄</span> Unduh PDF
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        @if (request()->filled('class_id') && request()->filled('subject_id'))
            <div class="bg-white rounded-lg shadow-lg p-6 border border-blue-100">
                <div class="border-b border-gray-200 pb-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Preview Rekap Absensi</h2>
                        <p class="text-gray-600 text-sm mt-1">
                            Kelas: <span class="font-semibold text-blue-600">{{ $selectedClass->class }}</span> | 
                            Mata Pelajaran: <span class="font-semibold text-blue-600">{{ $selectedSubject->subject }}</span>
                        </p>
                        <p class="text-gray-500 text-xs mt-0.5">
                            Rentang Waktu: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="bg-blue-50 px-4 py-2 rounded-lg text-sm text-blue-800 border border-blue-200">
                        Total Sesi: <span class="font-bold">{{ count($attendances) }}</span> kali pertemuan
                    </div>
                </div>

                @if (count($attendances) === 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center text-blue-700">
                        <p class="text-lg font-semibold">Tidak Ada Pertemuan Absensi</p>
                        <p class="text-sm mt-1">Tidak ditemukan sesi absensi untuk mata pelajaran dan kelas ini pada rentang waktu yang dipilih.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse border border-gray-200 min-w-max">
                            <thead class="text-xs text-white uppercase bg-blue-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 border border-blue-800 font-semibold sticky left-0 bg-blue-700 z-10">
                                        Nama Siswa
                                    </th>
                                    @foreach ($attendances as $att)
                                        <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center whitespace-nowrap" title="{{ \Carbon\Carbon::parse($att->attendance_date)->format('d F Y') }}">
                                            {{ \Carbon\Carbon::parse($att->attendance_date)->format('d/m') }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center bg-green-700">H</th>
                                    <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center bg-blue-700">I</th>
                                    <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center bg-yellow-700">S</th>
                                    <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center bg-red-700">A</th>
                                    <th scope="col" class="px-3 py-3 border border-blue-800 font-semibold text-center bg-gray-700">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matrix as $index => $row)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-blue-50/20' : 'bg-white' }} hover:bg-blue-100/30 transition">
                                        <td class="px-4 py-3 border border-gray-200 font-medium text-gray-900 sticky left-0 {{ $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                            {{ $row['student']->name }}
                                        </td>
                                        @foreach ($attendances as $att)
                                            @php
                                                $status = $row['attendance'][$att->id] ?? '-';
                                                $badgeClass = '';
                                                $text = '';
                                                if ($status == 'Hadir') {
                                                    $badgeClass = 'text-green-600 font-bold';
                                                    $text = 'H';
                                                } elseif ($status == 'Izin') {
                                                    $badgeClass = 'text-blue-600 font-bold';
                                                    $text = 'I';
                                                } elseif ($status == 'Sakit') {
                                                    $badgeClass = 'text-yellow-600 font-bold';
                                                    $text = 'S';
                                                } elseif ($status == 'Alpha') {
                                                    $badgeClass = 'text-red-600 font-bold';
                                                    $text = 'A';
                                                } else {
                                                    $badgeClass = 'text-gray-400';
                                                    $text = '-';
                                                }
                                            @endphp
                                            <td class="px-3 py-3 border border-gray-200 text-center {{ $badgeClass }}">
                                                {{ $text }}
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-3 border border-gray-200 text-center font-semibold text-green-700">{{ $row['hadir'] }}</td>
                                        <td class="px-3 py-3 border border-gray-200 text-center font-semibold text-blue-700">{{ $row['izin'] }}</td>
                                        <td class="px-3 py-3 border border-gray-200 text-center font-semibold text-yellow-700">{{ $row['sakit'] }}</td>
                                        <td class="px-3 py-3 border border-gray-200 text-center font-semibold text-red-700">{{ $row['alpha'] }}</td>
                                        <td class="px-3 py-3 border border-gray-200 text-center font-bold text-gray-800">{{ $row['percentage'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Legend -->
                    <div class="mt-6 border-t border-gray-100 pt-4 flex flex-wrap gap-4 text-xs text-gray-600">
                        <span class="font-semibold text-gray-800">Keterangan:</span>
                        <span><span class="text-green-600 font-bold">H</span> = Hadir</span>
                        <span><span class="text-blue-600 font-bold">I</span> = Izin</span>
                        <span><span class="text-yellow-600 font-bold">S</span> = Sakit</span>
                        <span><span class="text-red-600 font-bold">A</span> = Alpha</span>
                        <span><span class="font-bold">%</span> = Persentase Kehadiran</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Script to dynamically handle inputs -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.getElementById('period_type');
            const periodContainers = {
                weekly: document.getElementById('weekly_input_container'),
                monthly: document.getElementById('monthly_input_container'),
                semesterly: document.getElementById('semesterly_input_container')
            };

            function updateInputs() {
                const selectedVal = periodSelect.value;
                
                // Hide all and disable inputs inside them to prevent sending unwanted parameters
                Object.keys(periodContainers).forEach(key => {
                    const container = periodContainers[key];
                    container.classList.add('hidden');
                    const inputs = container.querySelectorAll('input, select');
                    inputs.forEach(input => input.disabled = true);
                });

                // Show active container and enable inputs
                if (periodContainers[selectedVal]) {
                    const activeContainer = periodContainers[selectedVal];
                    activeContainer.classList.remove('hidden');
                    const inputs = activeContainer.querySelectorAll('input, select');
                    inputs.forEach(input => input.disabled = false);
                }
            }

            periodSelect.addEventListener('change', updateInputs);
            
            // Run on init
            updateInputs();
        });
    </script>
</x-app-layout>
