<?php
/**
 * views/lecturers/form.php — Form Tambah/Ubah Data Dosen
 *
 * Form ini digunakan untuk dua keperluan (create & edit).
 * Controller menentukan judul dan action URL yang sesuai.
 *
 * Variabel yang diharapkan dari controller:
 *   - $formTitle   (string) : Judul form ('Tambah Dosen' atau 'Ubah Data Dosen').
 *   - $formAction  (string) : Action untuk URL form (misalnya 'create' atau 'edit&id=5').
 *   - $errors      (array)  : Daftar pesan error validasi (kosong jika tidak ada).
 *   - $lecturer    (array)  : Data dosen untuk mengisi field form.
 *   - $departments (array)  : Daftar departemen untuk dropdown select.
 *   - $pageTitle   (string) : Judul halaman (di-set oleh controller).
 */
?>

<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ==================== HEADING & TOMBOL KEMBALI ==================== -->
<div class="page-heading">
    <div>
        <h1><?= e($formTitle) ?></h1>
        <p>Isi informasi dosen pada formulir berikut.</p>
    </div>
    <a href="index.php?page=lecturers" class="btn btn-outline-secondary">Kembali</a>
</div>

<!-- ==================== CARD FORMULIR ==================== -->
<div class="card mx-auto" style="max-width:760px;">
    <div class="card-header py-3">Informasi Dosen</div>
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

        <!-- Form input data dosen -->
        <form method="post" action="index.php?page=lecturers&action=<?= e($formAction) ?>">
            <!-- CSRF token (hidden field) untuk keamanan -->
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <!-- Field: Nama lengkap -->
            <div class="mb-3">
                <label for="name" class="form-label">Nama lengkap</label>
                <input type="text" id="name" name="name" class="form-control"
                       maxlength="100" value="<?= e($lecturer['name']) ?>" required>
            </div>

            <!-- Baris: NIDN dan Nomor telepon (2 kolom pada layar medium ke atas) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nidn" class="form-label">NIDN</label>
                    <input type="text" id="nidn" name="nidn" class="form-control"
                           maxlength="20" value="<?= e($lecturer['nidn']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Nomor telepon</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           maxlength="20" value="<?= e($lecturer['phone']) ?>" required>
                </div>
            </div>

            <!-- Baris: Tanggal bergabung dan Departemen (2 kolom) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="join_date" class="form-label">Tanggal bergabung</label>
                    <input type="date" id="join_date" name="join_date" class="form-control"
                           value="<?= e($lecturer['join_date']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Departemen</label>
                    <!-- Dropdown pilihan departemen; opsi pertama = belum ditentukan -->
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">Belum ditentukan</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= e($department['id']) ?>"
                                <?= (string) $lecturer['department_id'] === (string) $department['id'] ? 'selected' : '' ?>>
                                <?= e($department['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Pilihan departemen dikelola melalui menu Data Departemen.</div>
                </div>
            </div>

            <!-- Tombol aksi: Simpan & Batal -->
            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?page=lecturers" class="btn btn-light border">Batal</a>
            </div>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
