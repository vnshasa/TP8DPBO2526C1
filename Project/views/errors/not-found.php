<!--
    views/errors/not-found.php — Halaman Error 404 (Not Found)

    Ditampilkan ketika user mengakses page atau action yang tidak
    terdaftar di routing (front controller). Halaman ini berdiri
    sendiri tanpa layout header/footer.
-->
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="card p-4">
            <h1 class="h3">404 - Halaman tidak ditemukan</h1>
            <p>Halaman yang diminta tidak tersedia.</p>
            <a href="index.php" class="btn btn-primary align-self-start">Kembali ke beranda</a>
        </div>
    </main>
</body>
</html>
