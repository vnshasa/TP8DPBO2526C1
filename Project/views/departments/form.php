<?php
/**
 * views/departments/form.php — Form Tambah/Ubah Departemen
 *
 * Form ini digunakan untuk dua keperluan (create & edit).
 * Controller menentukan judul dan action URL yang sesuai.
 *
 * Variabel yang diharapkan dari controller:
 *   - $formTitle   (string) : Judul form ('Tambah Departemen' atau 'Ubah Departemen').
 *   - $formAction  (string) : Action untuk URL form.
 *   - $errors      (array)  : Daftar pesan error validasi (kosong jika tidak ada).
 *   - $department  (array)  : Data departemen untuk mengisi field form.
 *   - $pageTitle   (string) : Judul halaman (di-set oleh controller).
 */
?>

<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ==================== HEADING & TOMBOL KEMBALI ==================== -->
<div class="page-heading">
    <div>
        <h1><?= e($formTitle) ?></h1>
        <p>Isi informasi departemen pada formulir berikut.</p>
    </div>
    <a href="index.php?page=departments" class="btn btn-outline-secondary">Kembali</a>
</div>

<!-- ==================== CARD FORMULIR ==================== -->
<div class="card mx-auto" style="max-width:760px;">
    <div class="card-header py-3">Informasi Departemen</div>
    <div class="card-body p-4">

        <!-- Tampilkan daftar error validasi jika ada -->
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <strong>Periksa kembali:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form input data departemen -->
        <form method="post" action="index.php?page=departments&action=<?= e($formAction) ?>">
            <!-- CSRF token (hidden field) untuk keamanan -->
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <!-- Field: Nama departemen (wajib) -->
            <div class="mb-3">
                <label for="department_name" class="form-label">Nama departemen</label>
                <input type="text" id="department_name" name="department_name" class="form-control"
                       maxlength="100" value="<?= e($department['department_name']) ?>" required>
            </div>

            <!-- Field: Fakultas (wajib) -->
            <div class="mb-3">
                <label for="faculty" class="form-label">Fakultas</label>
                <input type="text" id="faculty" name="faculty" class="form-control"
                       maxlength="100" value="<?= e($department['faculty']) ?>" required>
            </div>

            <!-- Field: Lokasi/Ruangan (opsional) -->
            <div class="mb-3">
                <label for="office_room" class="form-label">Lokasi/ruangan (opsional)</label>
                <input type="text" id="office_room" name="office_room" class="form-control"
                       maxlength="50" value="<?= e($department['office_room']) ?>">
            </div>

            <!-- Tombol aksi: Simpan & Batal -->
            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?page=departments" class="btn btn-light border">Batal</a>
            </div>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
