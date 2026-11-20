<?php
/**
 * index.php — Front Controller (Entry Point) Aplikasi LecturerHub
 *
 * File ini menjadi satu-satunya pintu masuk semua request HTTP.
 * Alur kerjanya:
 *   1. Memulai session untuk flash message & CSRF token.
 *   2. Memuat helper, konfigurasi, model, dan controller.
 *   3. Membuka koneksi database; menampilkan halaman error jika gagal.
 *   4. Menentukan controller & action berdasarkan parameter GET (?page=…&action=…).
 *   5. Menjalankan action yang diminta, atau menampilkan halaman 404.
 */

// Memulai session PHP — digunakan untuk CSRF token dan flash message.
session_start();

// --- Autoload manual: memuat seluruh file yang dibutuhkan. ---
require_once __DIR__ . '/helpers.php';                          // Fungsi-fungsi utilitas (escape, redirect, flash, CSRF)
require_once __DIR__ . '/config/Database.php';                  // Class koneksi database (singleton-style)
require_once __DIR__ . '/models/Lecturer.php';                  // Model untuk tabel `lecturers`
require_once __DIR__ . '/models/Department.php';                // Model untuk tabel `departments`
require_once __DIR__ . '/controllers/LecturerController.php';   // Controller CRUD dosen
require_once __DIR__ . '/controllers/DepartmentController.php'; // Controller CRUD departemen

// --- Koneksi Database ---
// Jika koneksi gagal (MySQL belum aktif, dsb.), tampilkan halaman error khusus.
try {
    $db = Database::getConnection();
} catch (Throwable $exception) {
    http_response_code(500);
    require __DIR__ . '/views/errors/database.php';
    exit;
}

// --- Inisialisasi Model ---
// Model bertugas berkomunikasi langsung dengan database (query, insert, update, delete).
$lecturerModel   = new Lecturer($db);
$departmentModel = new Department($db);

// --- Daftar Controller yang Tersedia ---
// Key array sesuai dengan nilai parameter `?page=…` di URL.
$controllers = [
    'lecturers'   => new LecturerController($lecturerModel, $departmentModel),
    'departments' => new DepartmentController($departmentModel),
];

// --- Routing Sederhana ---
// Membaca parameter URL untuk menentukan halaman dan aksi.
$page   = $_GET['page']   ?? 'lecturers';  // Default: halaman dosen
$action = $_GET['action'] ?? 'index';       // Default: tampil daftar (index)

// Daftar action yang diperbolehkan — mencegah pemanggilan method sembarangan.
$allowedActions = ['index', 'create', 'edit', 'delete'];

// Validasi: jika page atau action tidak dikenali, tampilkan halaman 404.
if (!isset($controllers[$page]) || !in_array($action, $allowedActions, true)) {
    http_response_code(404);
    require __DIR__ . '/views/errors/not-found.php';
    exit;
}

// --- Eksekusi Controller ---
// Memanggil method action (index/create/edit/delete) pada controller yang sesuai.
$controller = $controllers[$page];
$controller->$action();
