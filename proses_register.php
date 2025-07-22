<?php
session_start();
require_once 'koneksi.php'; // file koneksi ke database

// Ambil data dari form
$nama     = trim($_POST['nama']);
$nik     = trim($_POST['nik']);
$email    = trim($_POST['email']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];
$role = 'user'; // default role

// Validasi awal
if (empty($nama) || empty($nik) || empty($email) || empty($username) || empty($password) || empty($konfirmasi_password)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header("Location: register.php");
    exit;
}

if (!preg_match('/^352311[0-9]{10}$/', $nik)) {
    $_SESSION['error'] = 'NIK Tidak Valid, Anda Bukan Warga SOKO.';
    header("Location: register.php");
    exit;
}

// Cek apakah username sudah digunakan
$query = $koneksi->prepare("SELECT * FROM users WHERE nik = ?");
$query->bind_param("s", $nik);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = 'Nik sudah didaftarkan, silakan login atau hubungi admin.';
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid.';
    header("Location: register.php");
    exit;
}

if ($password !== $konfirmasi_password) {
    $_SESSION['error'] = 'Konfirmasi password tidak sesuai.';
    header("Location: register.php");
    exit;
}

// Cek apakah username sudah digunakan
$query = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = 'Username sudah digunakan, silakan pilih yang lain.';
    header("Location: register.php");
    exit;
}

// Enkripsi password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO users (nama_lengkap, nik, email, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nama, $nik, $email, $username, $hashed_password, $role);


if ($stmt->execute()) {
    $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
    header("Location: login.php");
    exit;
} else {
    $_SESSION['error'] = 'Registrasi gagal. Silakan coba lagi.';
    header("Location: register.php");
    exit;
}
?>
