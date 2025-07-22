<?php
session_start();
require 'koneksi.php';

// Cek admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// Ambil filter yang sama seperti di halaman utama
$filterKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterTanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Buat query filter seperti di halaman daftar
$where = "WHERE 1=1";
$params = [];
$types = '';

if ($filterKategori > 0) {
  $where .= " AND p.kategori_id = ?";
  $params[] = $filterKategori;
  $types .= 'i';
}

if (!empty($filterStatus)) {
  $where .= " AND p.status = ?";
  $params[] = $filterStatus;
  $types .= 's';
}

if (!empty($filterTanggal)) {
  $where .= " AND DATE(p.tanggal) = ?";
  $params[] = $filterTanggal;
  $types .= 's';
}

if (!empty($keyword)) {
  $where .= " AND (p.nama_lengkap LIKE ? OR p.kontak LIKE ? OR p.isi_pengaduan LIKE ? OR p.status LIKE ? OR p.kode_aduan LIKE ? OR p.tanggal LIKE ?)";
  $likeKeyword = "%$keyword%";
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
  $types .= 'ssssss';
}

// Query untuk data yang akan diexport (tanpa limit)
$sql = "SELECT p.*, k.nama_kategori 
        FROM pengaduan p 
        JOIN kategori_pengaduan k ON p.kategori_id = k.id
        $where
        ORDER BY p.tanggal DESC";

$stmt = $koneksi->prepare($sql);
if (!$stmt) {
  die("Prepare failed: " . $koneksi->error);
}
if (count($params) > 0) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Set header agar browser tahu ini file download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=pengaduan_export.csv');

// Tambahkan BOM UTF-8 supaya Excel tidak salah encoding karakter
echo "\xEF\xBB\xBF";

// Buat file output stream
$output = fopen('php://output', 'w');

// Tulis header kolom CSV
fputcsv($output, ['No', 'Kode Aduan', 'Nama', 'Kontak', 'Kategori', 'Isi Pengaduan', 'Status', 'Tanggal']);

// Tulis data baris per baris dengan format tanggal lebih mudah dibaca
$no = 1;
while ($row = $result->fetch_assoc()) {
  fputcsv($output, [
    $no++,
    $row['kode_aduan'],
    $row['nama_lengkap'],
    $row['kontak'],
    $row['nama_kategori'],
    $row['isi_pengaduan'],
    $row['status'],
    date('d-m-Y H:i', strtotime($row['tanggal']))
  ]);
}

fclose($output);
exit;
