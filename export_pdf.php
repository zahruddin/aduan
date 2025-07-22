<?php
session_start();
require 'koneksi.php';

// Include TCPDF library
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // sesuaikan path-nya

// Cek admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// Ambil filter seperti biasa
$filterKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterTanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Buat query filter
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
  for ($i=0; $i < 6; $i++) {
    $params[] = $likeKeyword;
  }
  $types .= 'ssssss';
}

// Query data tanpa limit
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

// Inisialisasi TCPDF landscape
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator('Sistem Pengaduan');
$pdf->SetAuthor('Admin Kecamatan');
$pdf->SetTitle('Laporan Pengaduan');
$pdf->SetSubject('Laporan Pengaduan Filtered');
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Judul dokumen
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Pengaduan', 0, 1, 'C');
$pdf->Ln(5);

// Set font isi tabel
$pdf->SetFont('helvetica', '', 10);

// Header tabel
$header = ['No', 'Kode Aduan', 'Nama', 'Kontak', 'Kategori', 'Isi Pengaduan', 'Status', 'Tanggal'];

// Lebar kolom (total 277 mm, A4 landscape lebar efektif ~297 mm, dikurangi margin kiri-kanan 10mm)
$w = [10, 30, 35, 30, 30, 90, 25, 27];

// Warna header
$pdf->SetFillColor(0, 102, 204);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('', 'B');
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// Warna isi tabel
$pdf->SetFillColor(224, 235, 255);
$pdf->SetTextColor(0);
$pdf->SetFont('');

$fill = 0;
$no = 1;

while ($row = $result->fetch_assoc()) {
    // Cek page break manual sebelum baris
    $startY = $pdf->GetY();
    $heightCell = 6;
    // Hitung tinggi yang diperlukan untuk isi_pengaduan (MultiCell)
    $nb = $pdf->getNumLines($row['isi_pengaduan'], $w[5]);
    $heightRow = max($heightCell, $nb * 6);
    if (($startY + $heightRow) > ($pdf->getPageHeight() - $pdf->getBreakMargin())) {
        $pdf->AddPage();
        // Tulis ulang header tabel setelah page break
        $pdf->SetFillColor(0, 102, 204);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->Cell($w[0], $heightRow, $no++, 'LR', 0, 'C', $fill);
    $pdf->Cell($w[1], $heightRow, $row['kode_aduan'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[2], $heightRow, $row['nama_lengkap'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[3], $heightRow, $row['kontak'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[4], $heightRow, $row['nama_kategori'], 'LR', 0, 'L', $fill);

    // Isi pengaduan menggunakan MultiCell supaya bisa multiline dan otomatis baris
    $pdf->SetXY($x + array_sum(array_slice($w, 0, 5)), $y);
    $pdf->MultiCell($w[5], 6, $row['isi_pengaduan'], 'LR', 'L', $fill, 0, '', '', true, 0, false, true, $heightRow, 'M');

    $pdf->SetXY($x + array_sum(array_slice($w, 0, 6)), $y);
    $pdf->Cell($w[6], $heightRow, $row['status'], 'LR', 0, 'C', $fill);
    $pdf->Cell($w[7], $heightRow, date('d-m-Y H:i', strtotime($row['tanggal'])), 'LR', 0, 'C', $fill);
    $pdf->Ln();

    $fill = !$fill;
}

// Garis bawah tabel
$pdf->Cell(array_sum($w), 0, '', 'T');

// Output PDF ke browser
$pdf->Output('laporan_pengaduan.pdf', 'I');
exit;
?>
