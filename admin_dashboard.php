<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Ambil data pengaduan
$sql = "SELECT p.*, k.nama_kategori FROM pengaduan p 
        JOIN kategori_pengaduan k ON p.kategori_id = k.id
        ORDER BY p.tanggal DESC";
$pengaduan = $koneksi->query($sql);
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
  <header class="bg-merah text-white py-4 shadow-sm">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <a href="index.php">
            <img src="logo.png" alt="Logo Kecamatan" class="me-2" style="height: 40px;" />
        </a>
            <h1 class="h4 mb-0">Dashboard Admin</h1>
      </div>
      <nav class="mt-3 mt-md-0">
        <span class="me-3">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
        <a href="logout.php" class="btn btn-light text-dark">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Konten -->
  <main class="container my-5 flex-grow-1">
    <h2 class="mb-4">Daftar Pengaduan</h2>

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
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = $pengaduan->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['kode_aduan']) ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['kontak']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['isi_pengaduan'])) ?></td>
                <td>
                  <?php if ($row['status'] == 'Menunggu'): ?>
                    <span class="badge bg-warning text-dark"><?= $row['status'] ?></span>
                  <?php elseif ($row['status'] == 'Diproses'): ?>
                    <span class="badge bg-primary"><?= $row['status'] ?></span>
                  <?php else: ?>
                    <span class="badge bg-success"><?= $row['status'] ?></span>
                  <?php endif; ?>
                </td>
                <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
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
