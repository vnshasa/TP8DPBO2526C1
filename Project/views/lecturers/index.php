<?php
/**
 * views/lecturers/index.php — Halaman Daftar Dosen
 *
 * Menampilkan:
 *   1. Judul halaman & tombol tambah dosen.
 *   2. Kartu ringkasan statistik (total dosen, departemen, dosen terhubung).
 *   3. Tabel data dosen dengan aksi edit & hapus.
 *
 * Variabel yang diharapkan dari controller:
 *   - $lecturers  (array)  : Daftar semua dosen.
 *   - $summary    (array)  : Statistik ringkasan.
 *   - $pageTitle   (string) : Judul halaman.
 */
?>

<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ==================== HEADING & TOMBOL TAMBAH ==================== -->
<div class="page-heading">
    <div>
        <h1>Data Dosen</h1>
        <p>Kelola informasi dosen dan departemen tempat dosen bernaung.</p>
    </div>
    <a href="index.php?page=lecturers&action=create" class="btn btn-primary">+ Tambah Dosen</a>
</div>

<!-- ==================== KARTU RINGKASAN STATISTIK ==================== -->
<div class="summary-grid">
    <!-- Total dosen terdaftar -->
    <div class="summary-card">
        <span class="summary-label">Total Dosen</span>
        <strong><?= e($summary['lecturers']) ?></strong>
        <span class="summary-note">Data dosen terdaftar</span>
    </div>
    <!-- Jumlah departemen yang tersedia -->
    <div class="summary-card">
        <span class="summary-label">Departemen</span>
        <strong><?= e($summary['departments']) ?></strong>
        <span class="summary-note">Unit akademik tersedia</span>
    </div>
    <!-- Dosen yang sudah memiliki departemen -->
    <div class="summary-card">
        <span class="summary-label">Dosen Terhubung</span>
        <strong><?= e($summary['assigned']) ?></strong>
        <span class="summary-note">Sudah memiliki departemen</span>
    </div>
</div>

<!-- ==================== TABEL DATA DOSEN ==================== -->
<div class="card">
    <!-- Header tabel: judul dan badge jumlah data -->
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span>Daftar Dosen</span>
        <span class="badge badge-soft rounded-pill"><?= count($lecturers) ?> data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Nama</th>
                    <th>NIDN</th>
                    <th>Telepon</th>
                    <th>Tanggal Bergabung</th>
                    <th>Departemen</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$lecturers): ?>
                    <!-- Pesan jika belum ada data dosen -->
                    <tr>
                        <td colspan="7" class="empty-state">
                            Belum ada data dosen. Klik Tambah Dosen untuk memasukkan data pertama.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lecturers as $lecturer): ?>
                        <tr>
                            <!-- Kolom data dosen -->
                            <td class="ps-4 fw-semibold">#<?= e($lecturer['id']) ?></td>
                            <td class="fw-semibold"><?= e($lecturer['name']) ?></td>
                            <td><?= e($lecturer['nidn']) ?></td>
                            <td><?= e($lecturer['phone']) ?></td>
                            <!-- Format tanggal dari YYYY-MM-DD menjadi DD-MM-YYYY -->
                            <td><?= e(date('d-m-Y', strtotime($lecturer['join_date']))) ?></td>
                            <td>
                                <?php if ($lecturer['department_name']): ?>
                                    <!-- Badge nama departemen -->
                                    <span class="badge badge-soft rounded-pill">
                                        <?= e($lecturer['department_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <!-- Teks abu-abu jika belum ada departemen -->
                                    <span class="text-secondary">Belum ditentukan</span>
                                <?php endif; ?>
                            </td>
                            <!-- Tombol aksi: Edit & Hapus -->
                            <td class="text-end pe-4 text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="index.php?page=lecturers&action=edit&id=<?= e($lecturer['id']) ?>">
                                    Edit
                                </a>
                                <!-- Form hapus: menggunakan POST + CSRF + konfirmasi JavaScript -->
                                <form method="post"
                                      action="index.php?page=lecturers&action=delete"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data dosen ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id" value="<?= e($lecturer['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
