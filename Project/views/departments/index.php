<?php
/**
 * views/departments/index.php — Halaman Daftar Departemen
 *
 * Menampilkan:
 *   1. Judul halaman & tombol tambah departemen.
 *   2. Kartu ringkasan statistik (total departemen, dosen terhubung, departemen terisi).
 *   3. Tabel data departemen dengan aksi edit & hapus.
 *
 * Variabel yang diharapkan dari controller:
 *   - $departments (array)  : Daftar semua departemen.
 *   - $summary     (array)  : Statistik ringkasan.
 *   - $pageTitle    (string) : Judul halaman.
 */
?>

<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ==================== HEADING & TOMBOL TAMBAH ==================== -->
<div class="page-heading">
    <div>
        <h1>Data Departemen</h1>
        <p>Kelola departemen dan lihat jumlah dosen pada setiap departemen.</p>
    </div>
    <a href="index.php?page=departments&action=create" class="btn btn-primary">+ Tambah Departemen</a>
</div>

<!-- ==================== KARTU RINGKASAN STATISTIK ==================== -->
<div class="summary-grid">
    <!-- Total departemen terdaftar -->
    <div class="summary-card">
        <span class="summary-label">Total Departemen</span>
        <strong><?= e($summary['departments']) ?></strong>
        <span class="summary-note">Unit akademik terdaftar</span>
    </div>
    <!-- Jumlah dosen yang sudah terhubung ke departemen -->
    <div class="summary-card">
        <span class="summary-label">Dosen Terhubung</span>
        <strong><?= e($summary['lecturers']) ?></strong>
        <span class="summary-note">Memiliki departemen</span>
    </div>
    <!-- Departemen yang memiliki minimal satu dosen -->
    <div class="summary-card">
        <span class="summary-label">Departemen Terisi</span>
        <strong><?= e($summary['occupied']) ?></strong>
        <span class="summary-note">Memiliki setidaknya satu dosen</span>
    </div>
</div>

<!-- ==================== TABEL DATA DEPARTEMEN ==================== -->
<div class="card">
    <!-- Header tabel: judul dan badge jumlah data -->
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span>Daftar Departemen</span>
        <span class="badge badge-soft rounded-pill"><?= count($departments) ?> data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Nama Departemen</th>
                    <th>Fakultas</th>
                    <th>Lokasi/Ruangan</th>
                    <th>Jumlah Dosen</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$departments): ?>
                    <!-- Pesan jika belum ada data departemen -->
                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada departemen. Tambahkan departemen agar bisa dipilih pada formulir dosen.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <!-- Kolom data departemen -->
                            <td class="ps-4 fw-semibold">#<?= e($department['id']) ?></td>
                            <td class="fw-semibold"><?= e($department['department_name']) ?></td>
                            <td><?= e($department['faculty']) ?></td>
                            <td><?= e($department['office_room'] ?: '-') ?></td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill">
                                    <?= e($department['lecturer_count']) ?> dosen
                                </span>
                            </td>
                            <!-- Tombol aksi: Edit & Hapus -->
                            <td class="text-end pe-4 text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="index.php?page=departments&action=edit&id=<?= e($department['id']) ?>">
                                    Edit
                                </a>
                                <!-- Form hapus: POST + CSRF + konfirmasi JavaScript -->
                                <form method="post"
                                      action="index.php?page=departments&action=delete"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus departemen ini? Data dosen tidak dihapus, tetapi relasi departemennya akan dikosongkan.')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id" value="<?= e($department['id']) ?>">
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
