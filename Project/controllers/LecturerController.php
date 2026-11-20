<?php
/**
 * LecturerController.php — Controller untuk Manajemen Data Dosen
 *
 * Menangani 4 action utama (CRUD):
 *   - index()  : Menampilkan daftar seluruh dosen.
 *   - create() : Form & proses tambah dosen baru.
 *   - edit()   : Form & proses ubah data dosen.
 *   - delete() : Proses hapus data dosen.
 *
 * Controller ini membutuhkan dua model:
 *   - Lecturer   : untuk operasi data dosen.
 *   - Department : untuk mengambil pilihan departemen di form.
 */
class LecturerController
{
    /** @var Lecturer   Model untuk akses data dosen. */
    private Lecturer $lecturerModel;

    /** @var Department Model untuk akses data departemen (dropdown form). */
    private Department $departmentModel;

    /**
     * Constructor — menerima dependency kedua model.
     *
     * @param Lecturer   $lecturerModel   Instance model Lecturer.
     * @param Department $departmentModel Instance model Department.
     */
    public function __construct(Lecturer $lecturerModel, Department $departmentModel)
    {
        $this->lecturerModel   = $lecturerModel;
        $this->departmentModel = $departmentModel;
    }

    // =========================================================================
    //  ACTION: INDEX — Tampilkan daftar dosen
    // =========================================================================

    /**
     * Tampilkan halaman daftar dosen beserta ringkasan statistik.
     *
     * Statistik yang dihitung:
     *   - Total dosen terdaftar.
     *   - Jumlah departemen yang tersedia.
     *   - Jumlah dosen yang sudah memiliki departemen (assigned).
     */
    public function index(): void
    {
        // Ambil semua dosen (JOIN dengan nama departemen).
        $lecturers         = $this->lecturerModel->getAll();
        $departmentOptions = $this->departmentModel->getOptions();

        // Data ringkasan untuk summary card di halaman index.
        $summary = [
            'lecturers'   => count($lecturers),
            'departments' => count($departmentOptions),
            'assigned'    => count(array_filter($lecturers, static function (array $lecturer): bool {
                return $lecturer['department_id'] !== null;
            })),
        ];

        $pageTitle = 'Data Dosen';

        // Render view daftar dosen.
        require __DIR__ . '/../views/lecturers/index.php';
    }

    // =========================================================================
    //  ACTION: CREATE — Tambah dosen baru
    // =========================================================================

    /**
     * Tampilkan form tambah dosen, lalu proses jika method POST.
     *
     * Alur:
     *   1. Tampilkan form kosong dengan dropdown departemen (GET).
     *   2. Jika disubmit (POST): validasi CSRF → baca data → validasi input.
     *   3. Jika lolos: simpan ke database dan redirect dengan flash sukses.
     *   4. Jika gagal: tampilkan kembali form dengan pesan error.
     */
    public function create(): void
    {
        $formTitle   = 'Tambah Dosen';
        $formAction  = 'create';
        $errors      = [];
        $departments = $this->departmentModel->getOptions();

        // Data default untuk mengisi form (semua kosong).
        $lecturer = [
            'id'            => '',
            'name'          => '',
            'nidn'          => '',
            'phone'         => '',
            'join_date'     => '',
            'department_id' => '',
        ];

        // Proses form saat method POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();

            $lecturer = $this->readFormData();
            $errors   = $this->validate($lecturer);

            if (!$errors) {
                try {
                    $this->lecturerModel->create($lecturer);
                    setFlash('success', 'Data dosen berhasil ditambahkan.');
                    redirectTo('lecturers');
                } catch (mysqli_sql_exception $exception) {
                    // Kode 1062 = duplicate entry (NIDN sudah ada di database).
                    $errors[] = $exception->getCode() === 1062
                        ? 'NIDN sudah digunakan oleh dosen lain.'
                        : 'Data dosen gagal disimpan. Periksa kembali data yang dimasukkan.';
                }
            }
        }

        // Render form (dengan data & error jika ada).
        require __DIR__ . '/../views/lecturers/form.php';
    }

    // =========================================================================
    //  ACTION: EDIT — Ubah data dosen
    // =========================================================================

    /**
     * Tampilkan form edit dosen, lalu proses jika method POST.
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
        $id       = positiveId($_GET['id'] ?? null);
        $lecturer = $id ? $this->lecturerModel->findById($id) : null;

        // Jika dosen tidak ditemukan, kembali ke daftar.
        if (!$lecturer) {
            setFlash('danger', 'Data dosen tidak ditemukan.');
            redirectTo('lecturers');
        }

        $formTitle   = 'Ubah Data Dosen';
        $formAction  = 'edit&id=' . $id;
        $errors      = [];
        $departments = $this->departmentModel->getOptions();

        // Proses form saat method POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();

            $lecturer       = $this->readFormData();
            $lecturer['id'] = $id; // Pastikan ID tetap dari URL, bukan form.
            $errors         = $this->validate($lecturer);

            if (!$errors) {
                try {
                    $this->lecturerModel->update($id, $lecturer);
                    setFlash('success', 'Data dosen berhasil diperbarui.');
                    redirectTo('lecturers');
                } catch (mysqli_sql_exception $exception) {
                    $errors[] = $exception->getCode() === 1062
                        ? 'NIDN sudah digunakan oleh dosen lain.'
                        : 'Data dosen gagal diperbarui.';
                }
            }
        }

        // Render form (dengan data & error jika ada).
        require __DIR__ . '/../views/lecturers/form.php';
    }

    // =========================================================================
    //  ACTION: DELETE — Hapus data dosen
    // =========================================================================

    /**
     * Proses penghapusan data dosen (hanya menerima method POST).
     */
    public function delete(): void
    {
        // Tolak jika bukan request POST (mencegah penghapusan via URL langsung).
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo('lecturers');
        }

        verifyCsrfToken();

        $id = positiveId($_POST['id'] ?? null);

        if ($id) {
            $this->lecturerModel->delete($id);
            setFlash('success', 'Data dosen berhasil dihapus.');
        } else {
            setFlash('danger', 'ID dosen tidak valid.');
        }

        redirectTo('lecturers');
    }

    // =========================================================================
    //  METHOD PRIVATE — Helper internal controller
    // =========================================================================

    /**
     * Baca dan bersihkan data dari form POST.
     *
     * Jika department_id kosong (belum ditentukan), nilainya di-set null
     * agar tersimpan sebagai NULL di database.
     *
     * @return array Data dosen yang sudah di-trim.
     */
    private function readFormData(): array
    {
        $departmentId = trim($_POST['department_id'] ?? '');

        return [
            'name'          => trim($_POST['name'] ?? ''),
            'nidn'          => trim($_POST['nidn'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'join_date'     => trim($_POST['join_date'] ?? ''),
            'department_id' => $departmentId === '' ? null : (int) $departmentId,
        ];
    }

    /**
     * Validasi data dosen.
     *
     * Aturan:
     *   - name:          wajib diisi, maksimal 100 karakter.
     *   - nidn:          wajib diisi, maksimal 20 karakter.
     *   - phone:         wajib diisi, maksimal 20 karakter.
     *   - join_date:     wajib diisi, format YYYY-MM-DD yang valid.
     *   - department_id: opsional, tapi jika diisi harus ada di tabel departments.
     *
     * @param  array $data Data dosen yang akan divalidasi.
     * @return array       Daftar pesan error (kosong jika valid).
     */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['name'] === '' || strlen($data['name']) > 100) {
            $errors[] = 'Nama wajib diisi dan maksimal 100 karakter.';
        }

        if ($data['nidn'] === '' || strlen($data['nidn']) > 20) {
            $errors[] = 'NIDN wajib diisi dan maksimal 20 karakter.';
        }

        if ($data['phone'] === '' || strlen($data['phone']) > 20) {
            $errors[] = 'Nomor telepon wajib diisi dan maksimal 20 karakter.';
        }

        // Validasi format tanggal: harus YYYY-MM-DD dan merupakan tanggal yang real.
        $date = DateTime::createFromFormat('Y-m-d', $data['join_date']);
        if (!$date || $date->format('Y-m-d') !== $data['join_date']) {
            $errors[] = 'Tanggal bergabung wajib diisi dengan tanggal yang valid.';
        }

        // Jika department_id diisi, pastikan departemen tersebut ada di database.
        if ($data['department_id'] !== null
            && !$this->departmentModel->findById($data['department_id'])) {
            $errors[] = 'Departemen yang dipilih tidak tersedia.';
        }

        return $errors;
    }
}
