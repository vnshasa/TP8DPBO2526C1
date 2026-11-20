-- =============================================================================
-- database.sql — Skrip Pembuatan Database & Data Awal Aplikasi LecturerHub
-- =============================================================================
-- Cara penggunaan:
--   1. Buka phpMyAdmin (http://localhost/phpmyadmin) atau MySQL CLI.
--   2. Import / jalankan seluruh isi file ini.
--   3. Database `tp8dpbo2526c1` beserta tabel dan data contoh akan otomatis dibuat.
-- =============================================================================

-- Buat database jika belum ada, dengan charset utf8mb4 (mendukung emoji & Unicode penuh).
CREATE DATABASE IF NOT EXISTS tp8dpbo2526c1
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Gunakan database yang baru dibuat.
USE tp8dpbo2526c1;

-- =============================================================================
-- TABEL: departments
-- Menyimpan data departemen/unit akademik.
-- Kolom:
--   - id              : Primary key auto-increment.
--   - department_name : Nama departemen (unik, tidak boleh duplikat).
--   - faculty         : Nama fakultas tempat departemen bernaung.
--   - office_room     : Lokasi/ruangan kantor departemen (opsional).
--   - created_at      : Waktu data dibuat (otomatis terisi).
-- =============================================================================
CREATE TABLE IF NOT EXISTS departments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    faculty         VARCHAR(100) NOT NULL,
    office_room     VARCHAR(50)  DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================================================
-- TABEL: lecturers
-- Menyimpan data dosen. Memiliki relasi many-to-one ke tabel departments
-- melalui kolom department_id (foreign key).
-- Kolom:
--   - id            : Primary key auto-increment.
--   - name          : Nama lengkap dosen.
--   - nidn          : Nomor Induk Dosen Nasional (unik).
--   - phone         : Nomor telepon dosen.
--   - join_date     : Tanggal dosen mulai bergabung.
--   - department_id : FK ke departments.id (nullable).
--   - created_at    : Waktu data dibuat (otomatis terisi).
--
-- Constraint foreign key:
--   - ON UPDATE CASCADE   : Jika id departemen berubah, ikut diperbarui.
--   - ON DELETE SET NULL  : Jika departemen dihapus, kolom ini menjadi NULL
--                           (data dosen tetap aman, tidak ikut terhapus).
-- =============================================================================
CREATE TABLE IF NOT EXISTS lecturers (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    nidn          VARCHAR(20)  NOT NULL UNIQUE,
    phone         VARCHAR(20)  NOT NULL,
    join_date     DATE         NOT NULL,
    department_id INT UNSIGNED DEFAULT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lecturers_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================================
-- DATA AWAL: Departemen
-- Data contoh agar aplikasi bisa langsung dicoba setelah database di-import.
-- =============================================================================
INSERT IGNORE INTO departments (department_name, faculty, office_room) VALUES
('Ilmu Komputer',          'FPMIPA', 'Gedung FPMIPA B Lantai 3'),
('Pendidikan Matematika',  'FPMIPA', 'Gedung FPMIPA A Lantai 2'),
('Pendidikan Fisika',      'FPMIPA', 'Gedung FPMIPA A Lantai 3'),
('Pendidikan Kimia',       'FPMIPA', 'Gedung FPMIPA B Lantai 2'),
('Pendidikan Biologi',     'FPMIPA', 'Gedung FPMIPA C Lantai 2'),
('Matematika',             'FPMIPA', 'Gedung FPMIPA A Lantai 1');

-- =============================================================================
-- DATA AWAL: Dosen
-- Seluruh data berikut merupakan data contoh/fiktif untuk keperluan demonstrasi.
-- NIDN dan nomor telepon BUKAN data identitas dosen sungguhan.
-- Setiap dosen dihubungkan ke departemen menggunakan subquery berdasarkan nama.
-- =============================================================================
INSERT IGNORE INTO lecturers (name, nidn, phone, join_date, department_id) VALUES
('Andi Setiawan',      '0401128501', '081200000001', '2018-08-01', (SELECT id FROM departments WHERE department_name = 'Ilmu Komputer')),
('Dewi Lestari',       '0415098702', '081200000002', '2020-02-15', (SELECT id FROM departments WHERE department_name = 'Ilmu Komputer')),
('Rina Kartika',       '0412039003', '081200000003', '2019-01-10', (SELECT id FROM departments WHERE department_name = 'Pendidikan Matematika')),
('Farhan Ramadhan',    '9990000004', '081200000004', '2017-07-17', (SELECT id FROM departments WHERE department_name = 'Pendidikan Matematika')),
('Rizky Maulana',      '9990000005', '081200000005', '2016-03-21', (SELECT id FROM departments WHERE department_name = 'Pendidikan Fisika')),
('Siti Nur Aisyah',    '9990000006', '081200000006', '2021-09-06', (SELECT id FROM departments WHERE department_name = 'Pendidikan Fisika')),
('Nabila Putri',       '9990000007', '081200000007', '2022-01-12', (SELECT id FROM departments WHERE department_name = 'Pendidikan Kimia')),
('Fajar Nugraha',      '9990000008', '081200000008', '2015-11-02', (SELECT id FROM departments WHERE department_name = 'Pendidikan Kimia')),
('Intan Permatasari',  '9990000009', '081200000009', '2020-06-18', (SELECT id FROM departments WHERE department_name = 'Pendidikan Biologi')),
('Dimas Pratama',      '9990000010', '081200000010', '2019-04-25', (SELECT id FROM departments WHERE department_name = 'Pendidikan Biologi')),
('Putri Anggraini',    '9990000011', '081200000011', '2023-02-13', (SELECT id FROM departments WHERE department_name = 'Matematika')),
('Rafi Alamsyah',      '9990000012', '081200000012', '2018-12-03', (SELECT id FROM departments WHERE department_name = 'Matematika'));
