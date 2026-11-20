<?php
/**
 * Database.php — Class Koneksi Database
 *
 * Menyediakan method statis untuk membuat koneksi MySQLi.
 * Konfigurasi host, username, password, dan nama database
 * dimuat dari file `db_config.php` yang terpisah.
 */
class Database
{
    /**
     * Buat dan kembalikan koneksi MySQLi baru.
     *
     * - Mengaktifkan mode exception agar error SQL dilempar sebagai
     *   mysqli_sql_exception, bukan hanya warning.
     * - Mengatur charset ke utf8mb4 untuk mendukung emoji & karakter unicode.
     *
     * @return mysqli Objek koneksi database yang siap digunakan.
     * @throws mysqli_sql_exception Jika koneksi gagal atau database tidak ditemukan.
     */
    public static function getConnection(): mysqli
    {
        // Aktifkan mode pelaporan error sebagai exception.
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Muat konstanta konfigurasi (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME).
        require_once __DIR__ . '/db_config.php';

        // Buat koneksi baru ke MySQL.
        $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Set charset utf8mb4 agar mendukung seluruh karakter Unicode.
        $connection->set_charset('utf8mb4');

        return $connection;
    }
}
