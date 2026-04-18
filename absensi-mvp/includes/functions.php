<?php
// includes/functions.php - Fungsi-fungsi Helper

session_start();

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Cek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Redirect jika bukan admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit;
    }
}

// Logout user
function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Sanitasi output untuk mencegah XSS
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Format waktu Indonesia
function formatWaktu($waktu) {
    if (!$waktu) return '-';
    $dt = new DateTime($waktu);
    return $dt->format('H:i:s');
}

// Format tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $dt = new DateTime($tanggal);
    return $dt->format('d') . ' ' . $bulan[(int)$dt->format('n')] . ' ' . $dt->format('Y');
}

// Hitung status kehadiran
function hitungStatus($jamMasuk) {
    if (!$jamMasuk) return 'alpha';
    
    $toleransi = WAKTU_MASUK_TOLERANSI;
    if ($jamMasuk > $toleransi) {
        return 'terlambat';
    }
    return 'hadir';
}
