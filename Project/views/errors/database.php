<!--
    views/errors/database.php — Halaman Error Koneksi Database

    Ditampilkan ketika koneksi ke MySQL gagal (misalnya MySQL belum aktif
    atau database belum dibuat). Halaman ini berdiri sendiri (tidak
    menggunakan layout header/footer) karena saat error database,
    aplikasi belum bisa berjalan normal.
-->
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Koneksi Database</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="alert alert-danger">
            <h1 class="h4">Database belum dapat diakses</h1>
            <p class="mb-2">
                Pastikan MySQL pada XAMPP sudah aktif dan file
                <code>database.sql</code> sudah diimpor melalui phpMyAdmin.
            </p>
            <p class="mb-0">
                Periksa juga konfigurasi pada
                <code>Project/config/db_config.php</code>.
            </p>
        </div>
    </main>
</body>
</html>
