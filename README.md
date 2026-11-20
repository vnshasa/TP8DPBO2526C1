# TP8DPBO2526C1 — LecturerHub

**Tugas Praktikum 8 · Desain dan Pemrograman Berbasis Objek (DPBO)**  
**Materi:** Model–View–Controller (MVC)  
**Bahasa dan teknologi:** PHP Native, MySQL, MySQLi, HTML, CSS, Bootstrap

LecturerHub merupakan aplikasi web sederhana untuk mengelola data dosen dan departemen. Project awal memiliki fitur pengelolaan data dosen (`lecturers`). Pada pengembangan TP8, struktur program disusun menggunakan pola arsitektur **Model–View–Controller (MVC)** dan ditambahkan tabel `departments` yang berelasi dengan tabel `lecturers`.

Aplikasi menyediakan operasi CRUD (Create, Read, Update, Delete) untuk data dosen maupun data departemen. Database juga dilengkapi data contoh agar halaman dapat langsung dicoba setelah database diimpor.

## Janji
Saya **Vanisha Septiani Auliaputri** dengan **NIM 2510735** mengerjakan Tugas Praktikum 8 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahanNya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.

---

## 1. Fitur Aplikasi

| Bagian | Fitur |
|---|---|
| Data Dosen | Menampilkan daftar dosen beserta departemennya, menambah, mengubah, dan menghapus data dosen. |
| Data Departemen | Menampilkan daftar departemen beserta jumlah dosen, menambah, mengubah, dan menghapus departemen. |
| Relasi Dosen–Departemen | Memilih departemen saat menambah atau mengubah data dosen. |
| Ringkasan Data | Menampilkan jumlah dosen, jumlah departemen, serta jumlah dosen yang sudah terhubung ke departemen. |
| Validasi Form | Memeriksa kolom wajib, batas panjang input, format tanggal, dan pilihan departemen. |
| Notifikasi | Menampilkan pesan berhasil atau gagal setelah proses tambah, ubah, maupun hapus. |

## 2. Struktur Repository

```text
TP8DPBO2526C1/
├── Project/
│   ├── assets/
│   │   ├── style.css
│   │   └── vendor/
│   │       ├── bootstrap.min.css
│   │       └── bootstrap.bundle.min.js
│   ├── config/
│   │   ├── Database.php
│   │   └── db_config.php
│   ├── controllers/
│   │   ├── DepartmentController.php
│   │   └── LecturerController.php
│   ├── models/
│   │   ├── Department.php
│   │   └── Lecturer.php
│   ├── views/
│   │   ├── departments/
│   │   │   ├── form.php
│   │   │   └── index.php
│   │   ├── errors/
│   │   │   ├── database.php
│   │   │   └── not-found.php
│   │   ├── layout/
│   │   │   ├── footer.php
│   │   │   └── header.php
│   │   └── lecturers/
│   │       ├── form.php
│   │       └── index.php
│   ├── helpers.php
│   └── index.php
├── Dokumentasi/
│   └── demo.mp4
├── database.sql
└── README.md
```

Folder `Project` berisi kode aplikasi, `database.sql` berisi pembuatan database beserta data awal, sedangkan folder `Dokumentasi` berisi satu video demonstrasi aplikasi.

---

## 3. Desain Program — Arsitektur MVC

Arsitektur MVC memisahkan tanggung jawab program menjadi tiga bagian utama. Pemisahan ini membuat kode untuk mengakses data, mengatur alur request, dan menampilkan halaman tidak tercampur dalam satu file.

```text
Pengguna
   |
   v
Project/index.php  (Front Controller / Router)
   |
   v
Controller
   |  menerima request dan memproses aksi pengguna
   v
Model
   |  menjalankan operasi data melalui query SQL
   v
Database MySQL
   |
   v
Model mengembalikan hasil pengolahan data
   |
   v
Controller memilih View
   |
   v
View menampilkan halaman kepada pengguna
```

### 3.1 Controller — Pengatur Alur Aplikasi

Controller menerima aksi dari pengguna, memanggil model yang diperlukan, melakukan validasi input, lalu menentukan view yang ditampilkan.

| File | Tanggung jawab |
|---|---|
| `LecturerController.php` | Mengatur halaman daftar dosen serta proses tambah, ubah, dan hapus dosen. Controller ini juga mengambil daftar departemen untuk pilihan pada formulir dosen. |
| `DepartmentController.php` | Mengatur halaman daftar departemen serta proses tambah, ubah, dan hapus departemen. Controller menghitung ringkasan jumlah dosen pada departemen. |

Kedua controller menyediakan action `index`, `create`, `edit`, dan `delete`. Proses yang mengubah data dilakukan melalui request POST.

### 3.2 Model — Pengelolaan Data

Model berhubungan langsung dengan database dan menjalankan query untuk membaca maupun mengubah data. Model tidak bertugas menampilkan HTML atau menentukan route aplikasi.

| File | Tanggung jawab |
|---|---|
| `Lecturer.php` | Mengambil daftar dosen beserta nama departemen, mencari dosen berdasarkan ID, menambah, memperbarui, dan menghapus data dosen. |
| `Department.php` | Mengambil daftar departemen beserta jumlah dosen, menyediakan pilihan departemen untuk formulir, mencari departemen berdasarkan ID, menambah, memperbarui, dan menghapus data departemen. |

### 3.3 View — Tampilan Pengguna

View berisi halaman HTML/PHP yang ditampilkan kepada pengguna. Data yang diperlukan halaman disiapkan oleh controller, kemudian ditampilkan melalui view.

| Folder/File | Tanggung jawab |
|---|---|
| `views/lecturers/` | Halaman daftar dosen dan formulir tambah/ubah dosen. |
| `views/departments/` | Halaman daftar departemen dan formulir tambah/ubah departemen. |
| `views/layout/` | Bagian header dan footer yang digunakan bersama oleh halaman aplikasi. |
| `views/errors/` | Tampilan pesan ketika koneksi database gagal atau route tidak ditemukan. |

### 3.4 Komponen Pendukung

| File/Folder | Tanggung jawab |
|---|---|
| `Project/index.php` | Menjadi front controller, memuat komponen aplikasi, membuat koneksi database, dan mengarahkan request ke controller berdasarkan parameter URL. |
| `Project/config/Database.php` | Membuat koneksi MySQLi dan mengatur charset koneksi. |
| `Project/config/db_config.php` | Menyimpan host, username, password, dan nama database. |
| `Project/helpers.php` | Menyediakan fungsi bantuan seperti escape output HTML, redirect, flash message, token CSRF, dan validasi ID. |
| `Project/assets/` | Menyimpan stylesheet aplikasi dan file Bootstrap lokal. |

### 3.5 Alur Request

1. Pengguna membuka aplikasi melalui `Project/index.php`.
2. Front controller membaca parameter `page` dan `action` pada URL.
3. Request diteruskan ke `LecturerController` atau `DepartmentController` sesuai halaman yang diminta.
4. Controller memanggil model untuk mengambil atau mengubah data.
5. Model menjalankan query ke database MySQL dan mengembalikan hasilnya.
6. Controller menyiapkan data dan memilih view yang sesuai.
7. View menampilkan hasil kepada pengguna.

Contoh alamat route:

| Halaman/Aksi | Contoh URL relatif |
|---|---|
| Daftar dosen | `index.php?page=lecturers&action=index` |
| Form tambah dosen | `index.php?page=lecturers&action=create` |
| Form ubah dosen | `index.php?page=lecturers&action=edit&id=1` |
| Daftar departemen | `index.php?page=departments&action=index` |
| Form tambah departemen | `index.php?page=departments&action=create` |
| Form ubah departemen | `index.php?page=departments&action=edit&id=1` |

---

## 4. Desain Database

Aplikasi menggunakan MySQL dengan nama database **`tp8dpbo2526c1`**. Database terdiri dari dua tabel, yaitu `departments` dan `lecturers`.

### 4.1 Daftar Tabel

| Nama Tabel | Keterangan |
|---|---|
| `departments` | Menyimpan data departemen, fakultas, dan lokasi/ruangan departemen. Tabel ini merupakan tabel tambahan pada TP8. |
| `lecturers` | Menyimpan data dosen. Tabel ini berasal dari project awal dan ditambahkan kolom `department_id` sebagai foreign key. |

### 4.2 Struktur Tabel `departments`

Tabel `departments` menyimpan informasi setiap departemen. Nilai `id` digunakan sebagai identitas utama departemen.

| Field | Tipe Data | Ketentuan | Keterangan |
|---|---|---|---|
| `id` | `INT UNSIGNED` | Primary Key, Auto Increment | ID unik untuk setiap departemen. |
| `department_name` | `VARCHAR(100)` | NOT NULL, UNIQUE | Nama departemen; tidak boleh sama dengan nama departemen lain. |
| `faculty` | `VARCHAR(100)` | NOT NULL | Nama fakultas tempat departemen berada. |
| `office_room` | `VARCHAR(50)` | NULL, DEFAULT NULL | Lokasi atau ruangan departemen; boleh dikosongkan. |
| `created_at` | `TIMESTAMP` | DEFAULT CURRENT_TIMESTAMP | Waktu data departemen dibuat. |

### 4.3 Struktur Tabel `lecturers`

Tabel `lecturers` menyimpan informasi dosen. Kolom `department_id` ditambahkan untuk menghubungkan dosen dengan departemen yang dipilih.

| Field | Tipe Data | Ketentuan | Keterangan |
|---|---|---|---|
| `id` | `INT UNSIGNED` | Primary Key, Auto Increment | ID unik untuk setiap dosen. |
| `name` | `VARCHAR(100)` | NOT NULL | Nama lengkap dosen. |
| `nidn` | `VARCHAR(20)` | NOT NULL, UNIQUE | Nomor identitas dosen; setiap nilai harus unik di dalam tabel. |
| `phone` | `VARCHAR(20)` | NOT NULL | Nomor telepon dosen. |
| `join_date` | `DATE` | NOT NULL | Tanggal dosen mulai bergabung. |
| `department_id` | `INT UNSIGNED` | Foreign Key, NULL | Mengacu ke `departments.id`; boleh NULL jika dosen belum ditetapkan ke departemen. |
| `created_at` | `TIMESTAMP` | DEFAULT CURRENT_TIMESTAMP | Waktu data dosen dibuat. |

### 4.4 Relasi Antartabel

Relasi yang digunakan adalah **one-to-many**: satu departemen dapat memiliki beberapa dosen. Setiap dosen dapat terhubung ke paling banyak satu departemen melalui `lecturers.department_id`. Karena kolom tersebut boleh `NULL`, dosen juga dapat tersimpan tanpa departemen.

```text
DEPARTMENTS                              LECTURERS
+-------------------------+              +-------------------------+
| PK  id                  |  1        N  | PK  id                  |
|     department_name     |--------------|     name                |
|     faculty             |              |     nidn                |
|     office_room         |              |     phone               |
|     created_at          |              |     join_date            |
+-------------------------+              | FK  department_id       |
                                         |     created_at          |
                                         +-------------------------+
```

Detail foreign key:

| Properti | Nilai |
|---|---|
| Nama constraint | `fk_lecturers_department` |
| Kolom pada tabel anak | `lecturers.department_id` |
| Tabel/kolom referensi | `departments.id` |
| `ON UPDATE` | `CASCADE` |
| `ON DELETE` | `SET NULL` |

Dengan aturan `ON DELETE SET NULL`, ketika sebuah departemen dihapus, data dosen yang sebelumnya terhubung tidak ikut dihapus. Nilai `department_id` pada dosen tersebut otomatis menjadi `NULL`. Sementara itu, `ON UPDATE CASCADE` meneruskan perubahan nilai ID departemen ke kolom foreign key yang mengacu padanya.

### 4.5 Data Awal

File `database.sql` membuat database dan tabel, lalu memasukkan data contoh untuk kebutuhan demonstrasi:

- **6 departemen**, yaitu Ilmu Komputer, Pendidikan Matematika, Pendidikan Fisika, Pendidikan Kimia, Pendidikan Biologi, dan Matematika.
- **12 dosen contoh/fiktif**, dengan dua dosen pada setiap departemen.

NIDN dan nomor telepon dalam data awal merupakan data demonstrasi, bukan identitas atau nomor kontak dosen sungguhan. Perintah `INSERT IGNORE` digunakan agar baris contoh yang memiliki nilai unik dan sudah tersedia tidak dimasukkan kembali saat skrip diimpor ulang.

---

## 5. Validasi dan Keamanan Dasar

Beberapa mekanisme yang diterapkan pada aplikasi:

- **Prepared statement MySQLi** digunakan pada query yang menerima parameter input, seperti pencarian berdasarkan ID, insert, update, dan delete.
- **HTML escaping** dilakukan melalui helper `e()` yang menggunakan `htmlspecialchars()` sebelum nilai ditampilkan di halaman.
- **CSRF token** dibuat dan disimpan dalam session, lalu diverifikasi pada proses POST.
- **Validasi input** memeriksa kolom wajib, batas panjang karakter, format tanggal, ID positif, dan keberadaan departemen yang dipilih.
- **NIDN unik** dijaga melalui constraint `UNIQUE` di database dan penanganan duplikasi pada proses penyimpanan data dosen.
- **Penghapusan data** dilakukan melalui request POST, bukan dengan membuka URL delete secara langsung.

---

## 6. Dokumentasi Demo

Dokumentasi hasil aplikasi menggunakan **satu video demonstrasi** yang memperlihatkan penggunaan LecturerHub dan pemeriksaan database. Video disimpan di folder `Dokumentasi`.

https://github.com/user-attachments/assets/36df99e1-8642-476f-8465-d7fd6897968a
---
