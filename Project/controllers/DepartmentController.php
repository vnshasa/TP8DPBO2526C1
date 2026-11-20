<?php
/**
 * DepartmentController.php — Controller untuk Manajemen Departemen
 *
 * Menangani 4 action utama (CRUD):
 *   - index()  : Menampilkan daftar seluruh departemen.
 *   - create() : Form & proses tambah departemen baru.
 *   - edit()   : Form & proses ubah data departemen.
 *   - delete() : Proses hapus departemen.
 *
 * Setiap action POST divalidasi dengan CSRF token dan aturan input.
 */
class DepartmentController
{
    /** @var Department Model yang digunakan untuk akses data departemen. */
    private Department $departmentModel;

    /**
     * Constructor — menerima dependency model departemen.
     *
     * @param Department $departmentModel Instance model Department.
     */
    public function __construct(Department $departmentModel)
    {
        $this->departmentModel = $departmentModel;
    }

    // =========================================================================
    //  ACTION: INDEX — Tampilkan daftar departemen
    // =========================================================================

    /**
     * Tampilkan halaman daftar departemen beserta ringkasan statistik.
     *
     * Statistik yang dihitung:
     *   - Total departemen yang terdaftar.
     *   - Total dosen yang sudah terhubung ke departemen.
     *   - Jumlah departemen yang memiliki setidaknya satu dosen.
     */
    public function index(): void
    {
        // Ambil semua departemen beserta jumlah dosennya.
        $departments = $this->departmentModel->getAll();

        // Hitung total dosen dari seluruh departemen.
        $totalLecturers = array_sum(array_map(static function (array $department): int {
            return (int) $department['lecturer_count'];
        }, $departments));

        // Hitung berapa departemen yang sudah memiliki dosen (count > 0).
        $occupiedDepartments = count(array_filter($departments, static function (array $department): bool {
            return (int) $department['lecturer_count'] > 0;
        }));

        // Data ringkasan untuk ditampilkan di summary card.
        $summary = [
            'departments' => count($departments),
            'lecturers'   => $totalLecturers,
            'occupied'    => $occupiedDepartments,
        ];

        $pageTitle = 'Data Departemen';

        // Render view daftar departemen.
        require __DIR__ . '/../views/departments/index.php';
    }

    // =========================================================================
    //  ACTION: CREATE — Tambah departemen baru
    // =========================================================================

    /**
     * Tampilkan form tambah departemen, lalu proses jika method POST.
     *
     * Alur:
     *   1. Tampilkan form kosong (GET).
     *   2. Jika form disubmit (POST): validasi CSRF → baca data → validasi input.
     *   3. Jika lolos validasi: simpan ke database dan redirect dengan flash sukses.
     *   4. Jika gagal: tampilkan kembali form dengan pesan error.
     */
    public function create(): void
    {
        $formTitle  = 'Tambah Departemen';
        $formAction = 'create';
        $errors     = [];

        // Data default untuk mengisi form (semua kosong).
        $department = [
            'id'              => '',
            'department_name' => '',
            'faculty'         => '',
            'office_room'     => '',
        ];

        // Proses form saat method POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();

            $department = $this->readFormData();
            $errors     = $this->validate($department);

            if (!$errors) {
                try {
                    $this->departmentModel->create($department);
                    setFlash('success', 'Departemen berhasil ditambahkan.');
                    redirectTo('departments');
                } catch (mysqli_sql_exception $exception) {
                    // Kode 1062 = duplicate entry (nama departemen sudah ada).
                    $errors[] = $exception->getCode() === 1062
                        ? 'Nama departemen sudah terdaftar.'
                        : 'Departemen gagal disimpan.';
                }
            }
        }

        // Render form (dengan data & error jika ada).
        require __DIR__ . '/../views/departments/form.php';
    }

    // =========================================================================
    //  ACTION: EDIT — Ubah data departemen
    // =========================================================================

    /**
     * Tampilkan form edit departemen, lalu proses jika method POST.
     *
     * Alur:
     *   1. Ambil ID dari query string; cari datanya di database.
     *   2. Jika tidak ditemukan: redirect dengan flash error.
     *   3. Jika ditemukan: tampilkan form berisi data saat ini (GET).
     *   4. Jika disubmit (POST): validasi → update → redirect.
     */
    public function edit(): void
    {
        // Validasi ID dari parameter GET.
        $id         = positiveId($_GET['id'] ?? null);
        $department = $id ? $this->departmentModel->findById($id) : null;

        // Jika departemen tidak ditemukan, kembali ke daftar.
        if (!$department) {
            setFlash('danger', 'Departemen tidak ditemukan.');
            redirectTo('departments');
        }

        $formTitle  = 'Ubah Departemen';
        $formAction = 'edit&id=' . $id;
        $errors     = [];

        // Proses form saat method POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();

            $department       = $this->readFormData();
            $department['id'] = $id; // Pastikan ID tetap dari URL, bukan form.
            $errors           = $this->validate($department);

            if (!$errors) {
                try {
                    $this->departmentModel->update($id, $department);
                    setFlash('success', 'Departemen berhasil diperbarui.');
                    redirectTo('departments');
                } catch (mysqli_sql_exception $exception) {
                    $errors[] = $exception->getCode() === 1062
                        ? 'Nama departemen sudah digunakan.'
                        : 'Departemen gagal diperbarui.';
                }
            }
        }

        // Render form (dengan data & error jika ada).
        require __DIR__ . '/../views/departments/form.php';
    }

    // =========================================================================
    //  ACTION: DELETE — Hapus departemen
    // =========================================================================

    /**
     * Proses penghapusan departemen (hanya menerima method POST).
     *
     * Karena foreign key ON DELETE SET NULL, data dosen yang terkait
     * tidak ikut terhapus — kolom department_id-nya menjadi NULL.
     */
    public function delete(): void
    {
        // Tolak jika bukan request POST (mencegah penghapusan via URL langsung).
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo('departments');
        }

        verifyCsrfToken();

        $id = positiveId($_POST['id'] ?? null);

        if ($id) {
            $this->departmentModel->delete($id);
            setFlash('success', 'Departemen berhasil dihapus. Data dosen tetap tersimpan; departemennya dikosongkan.');
        } else {
            setFlash('danger', 'ID departemen tidak valid.');
        }

        redirectTo('departments');
    }

    // =========================================================================
    //  METHOD PRIVATE — Helper internal controller
    // =========================================================================

    /**
     * Baca dan bersihkan data dari form POST.
     *
     * @return array Data departemen yang sudah di-trim.
     */
    private function readFormData(): array
    {
        return [
            'department_name' => trim($_POST['department_name'] ?? ''),
            'faculty'         => trim($_POST['faculty'] ?? ''),
            'office_room'     => trim($_POST['office_room'] ?? ''),
        ];
    }

    /**
     * Validasi data departemen.
     *
     * Aturan:
     *   - department_name: wajib diisi, maksimal 100 karakter.
     *   - faculty:         wajib diisi, maksimal 100 karakter.
     *   - office_room:     opsional, maksimal 50 karakter.
     *
     * @param  array $data Data departemen yang akan divalidasi.
     * @return array       Daftar pesan error (kosong jika valid).
     */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['department_name'] === '' || strlen($data['department_name']) > 100) {
            $errors[] = 'Nama departemen wajib diisi dan maksimal 100 karakter.';
        }

        if ($data['faculty'] === '' || strlen($data['faculty']) > 100) {
            $errors[] = 'Fakultas wajib diisi dan maksimal 100 karakter.';
        }

        if (strlen($data['office_room']) > 50) {
            $errors[] = 'Lokasi/ruangan maksimal 50 karakter.';
        }

        return $errors;
    }
}
