<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Konfigurasi pagination
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung total data
$totalQuery = $koneksi->query("SELECT COUNT(*) as total FROM pengaduan");
$totalData = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data sesuai halaman
$sql = "SELECT p.*, k.nama_kategori 
        FROM pengaduan p 
        JOIN kategori_pengaduan k ON p.kategori_id = k.id
        ORDER BY p.tanggal DESC
        LIMIT $start, $limit";
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
        <a href="profil.php" class="btn btn-outline-light text-light">Kelola Profile</a>
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
              <!-- <th>Kode Aduan</th> -->
              <th>Nama</th>
              <th>Kontak</th>
              <th>Kategori</th>
              <th>Isi Pengaduan</th>
              <!-- <th>Status</th> -->
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
              <?php $no = $start + 1; while ($row = $pengaduan->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['kontak']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['isi_pengaduan'])) ?></td>
                
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