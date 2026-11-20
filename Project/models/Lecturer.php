<?php
/**
 * Lecturer.php — Model Dosen
 *
 * Class ini menangani seluruh operasi database (CRUD)
 * untuk tabel `lecturers`. Setiap query yang menerima
 * input pengguna menggunakan prepared statement untuk
 * mencegah SQL Injection.
 */
class Lecturer
{
    /** @var mysqli Koneksi database yang diinjeksikan melalui constructor. */
    private mysqli $db;

    /**
     * Constructor — menerima koneksi database.
     *
     * @param mysqli $db Objek koneksi MySQLi.
     */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Ambil semua data dosen beserta nama departemennya.
     *
     * Menggunakan LEFT JOIN ke tabel departments sehingga dosen
     * yang belum memiliki departemen tetap tampil (department_name = NULL).
     * Diurutkan dari data terbaru (ID terbesar) ke terlama.
     *
     * @return array Daftar dosen (associative array).
     */
    public function getAll(): array
    {
        $sql = "SELECT l.id, l.name, l.nidn, l.phone, l.join_date,
                       l.department_id, d.department_name
                FROM lecturers l
                LEFT JOIN departments d ON d.id = l.department_id
                ORDER BY l.id DESC";

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Cari satu dosen berdasarkan ID.
     *
     * @param  int        $id ID dosen yang dicari.
     * @return array|null     Data dosen, atau null jika tidak ditemukan.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, nidn, phone, join_date, department_id
             FROM lecturers WHERE id = ?'
        );
        $stmt->bind_param('i', $id);       // 'i' = tipe integer
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Tambah data dosen baru ke database.
     *
     * @param array $data Data dosen: name, nidn, phone, join_date, department_id.
     * @throws mysqli_sql_exception Jika NIDN duplikat (kode 1062).
     */
    public function create(array $data): void
    {
        $name         = $data['name'];
        $nidn         = $data['nidn'];
        $phone        = $data['phone'];
        $joinDate     = $data['join_date'];
        $departmentId = $data['department_id'];

        $stmt = $this->db->prepare(
            'INSERT INTO lecturers (name, nidn, phone, join_date, department_id)
             VALUES (?, ?, ?, ?, ?)'
        );
        // 'ssssi' = 4 string + 1 integer
        $stmt->bind_param('ssssi', $name, $nidn, $phone, $joinDate, $departmentId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Perbarui data dosen yang sudah ada.
     *
     * @param int   $id   ID dosen yang akan diperbarui.
     * @param array $data Data baru: name, nidn, phone, join_date, department_id.
     * @throws mysqli_sql_exception Jika NIDN duplikat (kode 1062).
     */
    public function update(int $id, array $data): void
    {
        $name         = $data['name'];
        $nidn         = $data['nidn'];
        $phone        = $data['phone'];
        $joinDate     = $data['join_date'];
        $departmentId = $data['department_id'];

        $stmt = $this->db->prepare(
            'UPDATE lecturers
             SET name = ?, nidn = ?, phone = ?, join_date = ?, department_id = ?
             WHERE id = ?'
        );
        // 'ssssii' = 4 string + 2 integer (department_id & id)
        $stmt->bind_param('ssssii', $name, $nidn, $phone, $joinDate, $departmentId, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Hapus data dosen berdasarkan ID.
     *
     * @param int $id ID dosen yang akan dihapus.
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM lecturers WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
