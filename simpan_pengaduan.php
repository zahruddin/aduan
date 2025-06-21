<?php
require 'koneksi.php';

$nama = $_POST['name'];
$kontak = $_POST['contact'];
$kategori_id = $_POST['type'];
$isi = $_POST['message'];
$kode_aduan = 'ADU-' . strtoupper(uniqid());
$status = 'Menunggu';

$stmt = $koneksi->prepare("INSERT INTO pengaduan (kode_aduan, nama_lengkap, kontak, kategori_id, isi_pengaduan, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiss", $kode_aduan, $nama, $kontak, $kategori_id, $isi, $status);

if ($stmt->execute()) {
  header("Location: index.php?success=1&kode=" . urlencode($kode_aduan));
  exit;
} else {
  echo "Gagal menyimpan pengaduan: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>
