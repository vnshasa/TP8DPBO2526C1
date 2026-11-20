<?php
/**
 * Department.php — Model Departemen
 *
 * Class ini menangani seluruh operasi database (CRUD)
 * untuk tabel `departments`. Menggunakan prepared statement
 * pada setiap query yang menerima input pengguna demi keamanan
 * terhadap serangan SQL Injection.
 */
class Department
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
     * Ambil semua departemen beserta jumlah dosen di masing-masing departemen.
     *
     * Menggunakan LEFT JOIN agar departemen yang belum memiliki dosen
     * tetap muncul (lecturer_count = 0). Diurutkan berdasarkan nama A-Z.
     *
     * @return array Daftar departemen (associative array).
     */
    public function getAll(): array
    {
        $sql = "SELECT d.id, d.department_name, d.faculty, d.office_room,
                       COUNT(l.id) AS lecturer_count
                FROM departments d
                LEFT JOIN lecturers l ON l.department_id = d.id
                GROUP BY d.id, d.department_name, d.faculty, d.office_room
                ORDER BY d.department_name ASC";

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil daftar departemen ringkas (id & nama) untuk dropdown/select.
     *
     * Digunakan di form dosen untuk memilih departemen.
     *
     * @return array Array berisi ['id', 'department_name'] per departemen.
     */
    public function getOptions(): array
    {
        $result = $this->db->query(
            'SELECT id, department_name FROM departments ORDER BY department_name ASC'
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Cari satu departemen berdasarkan ID.
     *
     * @param  int        $id ID departemen yang dicari.
     * @return array|null     Data departemen, atau null jika tidak ditemukan.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, department_name, faculty, office_room FROM departments WHERE id = ?'
        );
        $stmt->bind_param('i', $id);       // 'i' = tipe integer
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Tambah departemen baru ke database.
     *
     * @param array $data Data departemen: department_name, faculty, office_room.
     * @throws mysqli_sql_exception Jika nama departemen duplikat (kode 1062).
     */
    public function create(array $data): void
    {
        $departmentName = $data['department_name'];
        $faculty        = $data['faculty'];
        $officeRoom     = $data['office_room'];

        $stmt = $this->db->prepare(
            'INSERT INTO departments (department_name, faculty, office_room) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $departmentName, $faculty, $officeRoom); // 'sss' = 3 string
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Perbarui data departemen yang sudah ada.
     *
     * @param int   $id   ID departemen yang akan diperbarui.
     * @param array $data Data baru: department_name, faculty, office_room.
     * @throws mysqli_sql_exception Jika nama departemen duplikat (kode 1062).
     */
    public function update(int $id, array $data): void
    {
        $departmentName = $data['department_name'];
        $faculty        = $data['faculty'];
        $officeRoom     = $data['office_room'];

        $stmt = $this->db->prepare(
            'UPDATE departments
             SET department_name = ?, faculty = ?, office_room = ?
             WHERE id = ?'
        );
        $stmt->bind_param('sssi', $departmentName, $faculty, $officeRoom, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Hapus departemen berdasarkan ID.
     *
     * Karena foreign key `fk_lecturers_department` menggunakan ON DELETE SET NULL,
     * data dosen yang terkait tidak ikut terhapus — kolom department_id-nya
     * akan otomatis menjadi NULL.
     *
     * @param int $id ID departemen yang akan dihapus.
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM departments WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
