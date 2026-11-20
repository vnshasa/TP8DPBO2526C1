<?php
/**
 * header.php — Template Bagian Atas Halaman (Layout Header)
 *
 * File ini di-include oleh setiap view dan berisi:
 *   - Deklarasi DOCTYPE & tag <html>
 *   - <head> (meta, title, CSS Bootstrap & custom)
 *   - Navbar navigasi utama
 *   - Flash message notification (jika ada)
 *   - Tag pembuka <main> sebagai wadah konten halaman
 *
 * Variabel yang diharapkan tersedia:
 *   - $pageTitle (opsional) : Judul halaman untuk tag <title>.
 */

// Ambil nama halaman aktif untuk menandai menu yang sedang aktif.
$currentPage = $_GET['page'] ?? 'lecturers';

// Ambil flash message (pesan notifikasi sekali tampil) jika ada.
$flash = getFlash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Judul halaman: gunakan $pageTitle jika tersedia, fallback ke nama aplikasi. -->
    <title><?= e($pageTitle ?? 'Lecturer Management System') ?></title>

    <!-- CSS: Bootstrap 5 (lokal) + stylesheet custom aplikasi. -->
    <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- ==================== NAVBAR ==================== -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#25224a;">
    <div class="container content-wrap">
        <!-- Brand / Logo -->
        <a class="navbar-brand" href="index.php?page=lecturers">
            Lecturer<span class="text-info">Hub</span>
        </a>

        <!-- Tombol toggler untuk tampilan mobile -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false"
                aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu navigasi utama -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <!-- Link ke halaman Data Dosen -->
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'lecturers' ? 'active' : '' ?>"
                       href="index.php?page=lecturers">Data Dosen</a>
                </li>
                <!-- Link ke halaman Data Departemen -->
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'departments' ? 'active' : '' ?>"
                       href="index.php?page=departments">Data Departemen</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ==================== KONTEN UTAMA ==================== -->
<main class="container content-wrap pb-3">
    <?php
    // Tampilkan flash message jika ada (notifikasi sukses/error dari action sebelumnya).
    if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mt-4" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
