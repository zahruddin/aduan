<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$filterKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterTanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';


// Konfigurasi pagination
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$where = "WHERE 1=1";
$params = [];
$types = '';

if ($filterKategori > 0) {
  $where .= " AND p.kategori_id = ?";
  $params[] = $filterKategori;
  $types .= 'i'; // integer
}

// Filter kategori
if ($filterKategori > 0) {
  $where .= " AND p.kategori_id = ?";
  $params[] = $filterKategori;
  $types .= 'i'; // integer
}

// Filter status
if (!empty($filterStatus)) {
  $where .= " AND p.status = ?";
  $params[] = $filterStatus;
  $types .= 's'; // string
}

// Filter tanggal (cocokkan tanggal tepat, bisa disesuaikan formatnya)
if (!empty($filterTanggal)) {
  $where .= " AND DATE(p.tanggal) = ?";
  $params[] = $filterTanggal;
  $types .= 's'; // string (format 'YYYY-MM-DD')
}

// Filter keyword
if (!empty($keyword)) {
  $where .= " AND (p.nama_lengkap LIKE ? OR p.kontak LIKE ? OR p.isi_pengaduan LIKE ? OR p.status LIKE ? OR p.kode_aduan LIKE ? OR p.tanggal LIKE ?)";
  $likeKeyword = "%$keyword%";
  $params[] = $likeKeyword;  // p.nama_lengkap
  $params[] = $likeKeyword;  // p.kontak
  $params[] = $likeKeyword;  // p.isi_pengaduan
  $params[] = $likeKeyword;  // p.status
  $params[] = $likeKeyword;  // p.kode_aduan
  $params[] = $likeKeyword;  // p.tanggal
  $types .= 'ssssss';
}



// Hitung total data dengan filter
$stmtTotal = $koneksi->prepare("SELECT COUNT(*) as total FROM pengaduan p $where");
if (!$stmtTotal) {
    die("Prepare failed: " . $koneksi->error);
}

if (count($params) > 0) {
    $stmtTotal->bind_param($types, ...$params);
}

$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);
$stmtTotal->close();

// Query data dengan filter dan pagination
$sql = "SELECT p.*, k.nama_kategori 
        FROM pengaduan p 
        JOIN kategori_pengaduan k ON p.kategori_id = k.id
        $where
        ORDER BY p.tanggal DESC
        LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= 'ii';

$stmt = $koneksi->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $koneksi->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$pengaduan = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .bg-merah {
      background-color: #b91c1c;
    }
    .btn-merah {
      background-color: #b91c1c;
      color: white;
    }
    .btn-merah:hover {
      background-color: #991b1b;
      color: white;
    }
  </style>
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">

  <!-- Header -->
  <?php include 'header.php'; ?>

    <!-- Konten -->
   <main class="container my-5 flex-grow-1">
  <h2 class="mb-4">Daftar Pengaduan</h2>

  <?php
  // Ambil semua kategori untuk filter
  $kategoriResult = $koneksi->query("SELECT id, nama_kategori FROM kategori_pengaduan");
  ?>

  <!-- Form Filter TETAP tampil -->
  <form method="GET" class="row g-1 mb-4">
    <div class="col-md-2">
      <a href="export_pengaduan.php?kategori=<?= $filterKategori ?>&status=<?= urlencode($filterStatus) ?>&tanggal=<?= urlencode($filterTanggal) ?>&search=<?= urlencode($keyword) ?>" class="btn btn-success">Export CSV</a>
    </div>
    <div class="col-md-2">
      <a href="export_pdf.php?kategori=<?= $filterKategori ?>&status=<?= urlencode($filterStatus) ?>&tanggal=<?= urlencode($filterTanggal) ?>&search=<?= urlencode($keyword) ?>" class="btn btn-primary mb-3">Export PDF</a>  
    </div>
    <hr>
    <div class="col-md-2">
      <select name="kategori" class="form-select">
        <option value="0">Semua Kategori</option>
        <?php while ($kategori = $kategoriResult->fetch_assoc()): ?>
          <option value="<?= $kategori['id'] ?>" <?= ($filterKategori === (int)$kategori['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($kategori['nama_kategori']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">Semua Status</option>
        <?php 
        $statusOptions = ['Menunggu', 'Diproses', 'Selesai'];
        foreach ($statusOptions as $statusOpt): ?>
          <option value="<?= $statusOpt ?>" <?= ($filterStatus === $statusOpt) ? 'selected' : '' ?>>
            <?= $statusOpt ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($filterTanggal) ?>" />
    </div>

    <div class="col-md-2">
      <input type="text" name="search" class="form-control" placeholder="Cari nama, kontak, status atau isi" value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="col-md-1">
      <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  <?php if ($pengaduan->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Kode Aduan</th>
            <th>Nama</th>
            <th>Kontak</th>
            <th>Kategori</th>
            <th>Isi Pengaduan</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = $start + 1; 
          $status_options = ['Menunggu', 'Diproses', 'Selesai'];

          while ($row = $pengaduan->fetch_assoc()): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['kode_aduan']) ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($row['kontak']) ?></td>
            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['isi_pengaduan'])) ?></td>
            <td>
              <form method="post" action="aksi_pengaduan.php" class="d-flex gap-1">
                <input type="hidden" name="id_pengaduan" value="<?= $row['id'] ?>">
                <input type="hidden" name="aksi" value="update_status">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <?php foreach ($status_options as $status): ?>
                    <option value="<?= $status ?>" <?= ($row['status'] === $status) ? 'selected' : '' ?>>
                      <?= $status ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
            <td>
              <form method="post" action="aksi_pengaduan.php" class="d-flex gap-1 flex-wrap">
                <input type="hidden" name="id_pengaduan" value="<?= $row['id'] ?>">
                <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-danger"
                  onclick="return confirm('Yakin ingin menghapus aduan ini?')">Hapus</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-center mt-4">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Sebelumnya</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Berikutnya</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  <?php else: ?>
    <div class="alert alert-info">Belum ada pengaduan yang masuk.</div>
  <?php endif; ?>
</main>



  <!-- Footer -->
  <footer class="bg-secondary text-white text-center py-4 mt-auto">
    <p class="fw-bold">Kontak Kecamatan</p>
    <p>Jl. Raya Sokosari No. 231, Soko Tuban</p>
    <p>Telepon: 021-123456 | WhatsApp: 0812-3456-7890</p>
    <p>Email: pengaduan@kec-sejahtera.go.id</p>
    <p class="text-light small mt-2">&copy; 2025 Kecamatan Soko</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>