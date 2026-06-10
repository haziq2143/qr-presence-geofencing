<?php

namespace App\Utils;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class AttendanceHelper
{
    // Koordinat sekolah (latitude, longitude)
    const SCHOOL_LAT = -6.0462467;
    const SCHOOL_LNG = 106.0518361;
    const MAX_DISTANCE_METERS = 100; // 100 meter radius

    /**
     * Generate dynamic QR code dengan kode unik
     */
    public static function generateQrCode(): array
    {
        // Generate unique code
        $code = strtoupper(Str::random(8));

        // Generate QR code as SVG
        $qrCode = QrCode::size(200)->generate($code);

        return [
            'code' => $code,
            'qr' => $qrCode,
            'svg' => $qrCode
        ];
    }

    /**
     * Hitung jarak antara dua koordinat menggunakan Haversine formula (dalam meter)
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Validasi apakah koordinat siswa dalam radius sekolah
     */
    public static function isWithinSchoolRadius($studentLat, $studentLng): bool
    {
        if ($studentLat === null || $studentLng === null) {
            return false;
        }

        $distance = self::calculateDistance(
            self::SCHOOL_LAT,
            self::SCHOOL_LNG,
            $studentLat,
            $studentLng
        );

        return $distance <= self::MAX_DISTANCE_METERS;
    }

    /**
     * Get jarak dari sekolah
     */
    public static function getDistanceFromSchool($studentLat, $studentLng): float
    {
        if ($studentLat === null || $studentLng === null) {
            return 9999; // Return large number jika tidak ada koordinat
        }

        return self::calculateDistance(
            self::SCHOOL_LAT,
            self::SCHOOL_LNG,
            $studentLat,
            $studentLng
        );
    }

    /**
     * Format pesan error untuk jarak
     */
    public static function getDistanceErrorMessage($distance): string
    {
        $metersAway = round($distance - self::MAX_DISTANCE_METERS, 1);
        return "Anda berada {$metersAway} meter di luar radius sekolah. Harap datang ke lokasi sekolah.";
    }
}
