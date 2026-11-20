<?php
/**
 * helpers.php — Kumpulan Fungsi Utilitas Global
 *
 * File ini menyediakan fungsi-fungsi pembantu yang digunakan
 * di seluruh aplikasi (controller maupun view).
 *
 * Daftar fungsi:
 *   - e()              : Escape output HTML (mencegah XSS).
 *   - redirectTo()     : Redirect ke halaman tertentu.
 *   - setFlash()       : Menyimpan pesan notifikasi ke session.
 *   - getFlash()       : Mengambil dan menghapus pesan notifikasi dari session.
 *   - csrfToken()      : Membuat/mengambil CSRF token.
 *   - verifyCsrfToken(): Memverifikasi CSRF token dari form POST.
 *   - positiveId()     : Memvalidasi & mengonversi nilai menjadi integer positif.
 */

/**
 * Escape string untuk output HTML agar aman dari serangan XSS.
 *
 * Contoh: e('<script>') menghasilkan '&lt;script&gt;'
 *
 * @param  mixed  $value  Nilai yang akan di-escape.
 * @return string         Nilai yang sudah aman untuk ditampilkan di HTML.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect (alihkan) browser ke halaman tertentu lalu hentikan eksekusi.
 *
 * @param string $page   Nama page tujuan (misalnya 'lecturers' atau 'departments').
 * @param string $action Nama action tujuan (default: 'index').
 */
function redirectTo(string $page, string $action = 'index'): void
{
    header('Location: index.php?page=' . urlencode($page) . '&action=' . urlencode($action));
    exit;
}

/**
 * Simpan pesan flash ke session.
 *
 * Flash message hanya ditampilkan sekali pada halaman berikutnya,
 * kemudian otomatis dihapus (lihat getFlash).
 *
 * @param string $type    Tipe alert Bootstrap: 'success', 'danger', 'warning', dll.
 * @param string $message Isi pesan yang akan ditampilkan.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Ambil pesan flash dari session, lalu hapus agar tidak muncul lagi.
 *
 * @return array|null Array berisi 'type' dan 'message', atau null jika tidak ada.
 */
function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']); // Hapus setelah dibaca (one-time display)

    return $flash;
}

/**
 * Buat atau ambil CSRF token yang tersimpan di session.
 *
 * Token ini disisipkan sebagai hidden field di setiap form POST
 * untuk mencegah serangan Cross-Site Request Forgery (CSRF).
 *
 * @return string Token CSRF (hex string 64 karakter).
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // Buat token baru jika belum ada di session.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verifikasi CSRF token yang dikirim melalui form POST.
 *
 * Jika token tidak cocok atau tidak valid, kembalikan HTTP 419
 * dan hentikan eksekusi. Ini melindungi dari request palsu
 * yang dikirim dari situs lain.
 */
function verifyCsrfToken(): void
{
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken   = $_SESSION['csrf_token'] ?? '';

    // Gunakan hash_equals() untuk perbandingan yang aman terhadap timing attack.
    if (
        !is_string($submittedToken) || !is_string($sessionToken)
        || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)
    ) {
        http_response_code(419);
        exit('Permintaan tidak valid. Muat ulang halaman, lalu coba kembali.');
    }
}

/**
 * Validasi dan konversi nilai menjadi integer positif (>= 1).
 *
 * Digunakan untuk memvalidasi parameter ID dari URL/form
 * sebelum diproses lebih lanjut.
 *
 * @param  mixed    $value Nilai mentah (biasanya dari $_GET atau $_POST).
 * @return int|null        Integer positif jika valid, null jika tidak.
 */
function positiveId($value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    return $id === false ? null : $id;
}
