<?php
// Cek apakah user sudah login
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = isset($_POST['id_pengaduan']) ? intval($_POST['id_pengaduan']) : 0;
  $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';

  if ($id > 0 && in_array($aksi, ['diproses', 'selesai', 'hapus'])) {
    if ($aksi === 'diproses') {
      $stmt = $koneksi->prepare("UPDATE pengaduan SET status = 'Diproses' WHERE id = ?");
    } elseif ($aksi === 'selesai') {
      $stmt = $koneksi->prepare("UPDATE pengaduan SET status = 'Selesai' WHERE id = ?");
    } elseif ($aksi === 'hapus') {
      $stmt = $koneksi->prepare("DELETE FROM pengaduan WHERE id = ?");
    }

    // Bind param hanya kalau query berhasil disiapkan
    if ($stmt) {
      $stmt->bind_param("i", $id);
      $stmt->execute();
    } else {
      echo "Gagal menyiapkan query.";
      exit;
    }
  }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
