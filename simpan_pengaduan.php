<?php
session_start();
require 'koneksi.php';

// Cek login dan role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id']; // ambil id user dari session
$nama = $_POST['name'];
$kontak = $_POST['contact'];
$kategori_id = $_POST['type'];
$isi = $_POST['message'];
$kode_aduan = 'ADU-' . strtoupper(uniqid());
$status = 'Menunggu';

// Tambahkan user_id di query INSERT
$stmt = $koneksi->prepare("INSERT INTO pengaduan (kode_aduan, user_id, nama_lengkap, kontak, kategori_id, isi_pengaduan, status) VALUES (?, ?, ?, ?, ?, ?, ?)");

// ubah binding param, user_id bertipe integer (i)
$stmt->bind_param("sississ", $kode_aduan, $user_id, $nama, $kontak, $kategori_id, $isi, $status);

if ($stmt->execute()) {
  header("Location: pengaduan.php?success=1&kode=" . urlencode($kode_aduan));
  exit;
} else {
  echo "Gagal menyimpan pengaduan: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>
