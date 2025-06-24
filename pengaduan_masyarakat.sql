-- -----------------------------------------------------
-- DATABASE: pengaduan_masyarakat
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS pengaduan_masyarakat;
USE pengaduan_masyarakat;

-- -----------------------------------------------------
-- TABEL: users (Admin Login)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL
);

-- Admin Default (username: admin, password: admin123)
INSERT INTO users (username, password, nama_lengkap)
VALUES (
  'admin',
  '$2y$10$CVb0slQHhQ5TMJ.uduhK7uB9M9iWkU8O0kdutt8o9Vk.LjVlHOH7S', --  admin
  'Administrator'
);

-- -----------------------------------------------------
-- TABEL: kategori_pengaduan
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori_pengaduan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
);

-- Data Kategori Awal
INSERT INTO kategori_pengaduan (nama_kategori) VALUES 
  ('Pelayanan Administrasi'),
  ('Fasilitas Umum'),
  ('Keamanan / Ketertiban'),
  ('Lainnya');

-- -----------------------------------------------------
-- TABEL: pengaduan
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaduan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_aduan VARCHAR(20) NOT NULL UNIQUE,
  nama_lengkap VARCHAR(100) NOT NULL,
  kontak VARCHAR(100) NOT NULL,
  kategori_id INT NOT NULL,
  isi_pengaduan TEXT NOT NULL,
  status ENUM('Menunggu','Diproses','Selesai') DEFAULT 'Menunggu',
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori_pengaduan(id)
);
