<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Pastikan metode adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Metode request tidak valid.";
    exit;
}

// Ambil data dari form
$id_pengaduan = isset($_POST['id_pengaduan']) ? intval($_POST['id_pengaduan']) : 0;
$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$role = $_SESSION['role'];

// Validasi ID pengaduan
if ($id_pengaduan <= 0) {
    echo "ID pengaduan tidak valid.";
    exit;
}

if ($aksi === 'hapus') {
    // Aksi hapus bisa dilakukan oleh user maupun admin
    $stmt = $koneksi->prepare("DELETE FROM pengaduan WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_pengaduan);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Gagal menyiapkan query hapus.";
        exit;
    }

} elseif ($aksi === 'update_status') {
    // Aksi update status hanya boleh dilakukan oleh admin
    if ($role !== 'admin') {
        echo "Anda tidak memiliki izin untuk mengubah status.";
        exit;
    }

    $allowed_status = ['Menunggu', 'Diproses', 'Selesai'];
    if (!in_array($status, $allowed_status)) {
        echo "Status tidak valid.";
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE pengaduan SET status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $id_pengaduan);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Gagal menyiapkan query update.";
        exit;
    }

} else {
    echo "Aksi tidak dikenali.";
    exit;
}

// Redirect kembali ke halaman sebelumnya
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
exit;
