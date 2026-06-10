<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Absensi Siswa</title>
    <style>
        @page {
            size: landscape;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #eab308;
            padding-bottom: 10px;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #ca8a04;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 11px;
            color: #666;
            margin: 5px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .meta-label {
            width: 120px;
            font-weight: bold;
            color: #555;
        }
        .meta-value {
            color: #333;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }
        .attendance-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .attendance-table th {
            background-color: #ca8a04;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #a16207;
            padding: 6px 4px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .attendance-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
        }
        .attendance-table td.student-name {
            text-align: left;
            font-weight: bold;
            background-color: #fefcf3;
            padding-left: 8px;
            width: 180px;
        }
        /* Summary headers / cells */
        .th-summary {
            background-color: #4b5563 !important;
            border: 1px solid #374151 !important;
        }
        .th-h { background-color: #15803d !important; border: 1px solid #166534 !important; }
        .th-i { background-color: #1d4ed8 !important; border: 1px solid #1e40af !important; }
        .th-s { background-color: #b45309 !important; border: 1px solid #92400e !important; }
        .th-a { background-color: #b91c1c !important; border: 1px solid #991b1b !important; }
        
        /* Attendance status styles */
        .status-h { color: #15803d; font-weight: bold; }
        .status-i { color: #1d4ed8; font-weight: bold; }
        .status-s { color: #b45309; font-weight: bold; }
        .status-a { color: #b91c1c; font-weight: bold; }
        .status-none { color: #9ca3af; }

        .cell-summary {
            font-weight: bold;
            background-color: #f9fafb;
        }
        .legend {
            margin-top: 25px;
            font-size: 9px;
            color: #666;
            border-top: 1px dashed #ddd;
            padding-top: 8px;
        }
        .legend-title {
            font-weight: bold;
            margin-right: 10px;
            color: #444;
        }
        .legend-item {
            margin-right: 15px;
        }
        .legend-badge {
            font-weight: bold;
            margin-right: 2px;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 8px;
            color: #999;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="header-title">Rekap Absensi Siswa</h1>
        <p class="header-subtitle">Laporan data kehadiran per mata pelajaran dan kelas</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Mata Pelajaran</td>
            <td class="meta-value">: {{ $selectedSubject->subject }}</td>
            <td class="meta-label">Kelas</td>
            <td class="meta-value">: {{ $selectedClass->class }}</td>
        </tr>
        <tr>
            <td class="meta-label">Guru Pengajar</td>
            <td class="meta-value">: {{ $selectedSubject->teacher->name }}</td>
            <td class="meta-label">Rentang Waktu</td>
            <td class="meta-value">: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Pertemuan</td>
            <td class="meta-value">: {{ count($attendances) }} kali pertemuan</td>
            <td class="meta-label">Dicetak Pada</td>
            <td class="meta-value">: {{ now()->format('d M Y H:i:s') }}</td>
        </tr>
    </table>

    <table class="attendance-table">
        <thead>
            <tr>
                <th scope="col" style="text-align: left; padding-left: 8px;">Nama Siswa</th>
                @foreach ($attendances as $att)
                    <th scope="col">
                        {{ \Carbon\Carbon::parse($att->attendance_date)->format('d/m') }}
                    </th>
                @endforeach
                <th scope="col" class="th-h">H</th>
                <th scope="col" class="th-i">I</th>
                <th scope="col" class="th-s">S</th>
                <th scope="col" class="th-a">A</th>
                <th scope="col" class="th-summary">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matrix as $row)
                <tr>
                    <td class="student-name">{{ $row['student']->name }}</td>
                    @foreach ($attendances as $att)
                        @php
                            $status = $row['attendance'][$att->id] ?? '-';
                            $class = 'status-none';
                            $text = '-';
                            if ($status == 'Hadir') { $class = 'status-h'; $text = 'H'; }
                            elseif ($status == 'Izin') { $class = 'status-i'; $text = 'I'; }
                            elseif ($status == 'Sakit') { $class = 'status-s'; $text = 'S'; }
                            elseif ($status == 'Alpha') { $class = 'status-a'; $text = 'A'; }
                        @endphp
                        <td class="{{ $class }}">{{ $text }}</td>
                    @endforeach
                    <td class="cell-summary" style="color: #15803d;">{{ $row['hadir'] }}</td>
                    <td class="cell-summary" style="color: #1d4ed8;">{{ $row['izin'] }}</td>
                    <td class="cell-summary" style="color: #b45309;">{{ $row['sakit'] }}</td>
                    <td class="cell-summary" style="color: #b91c1c;">{{ $row['alpha'] }}</td>
                    <td class="cell-summary">{{ $row['percentage'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <span class="legend-title">Keterangan Status:</span>
        <span class="legend-item"><span class="legend-badge status-h">H</span> Hadir</span>
        <span class="legend-item"><span class="legend-badge status-i">I</span> Izin</span>
        <span class="legend-item"><span class="legend-badge status-s">S</span> Sakit</span>
        <span class="legend-item"><span class="legend-badge status-a">A</span> Alpha</span>
        <span class="legend-item"><span class="legend-badge" style="color: #333;">%</span> Persentase Kehadiran</span>
    </div>

    <div class="footer">
        Halaman 1 dari 1 | Sistem Absensi Online Sekolah
    </div>
</body>
</html>
