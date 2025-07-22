<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login dan merupakan user (bukan admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Konfigurasi pagination
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung total data khusus user ini
$stmtTotal = $koneksi->prepare("SELECT COUNT(*) as total FROM pengaduan WHERE user_id = ?");
$stmtTotal->bind_param("i", $user_id);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);
$stmtTotal->close();

// Ambil data sesuai halaman dan user
$sql = "SELECT p.*, k.nama_kategori 
        FROM pengaduan p 
        JOIN kategori_pengaduan k ON p.kategori_id = k.id
        WHERE p.user_id = ?
        ORDER BY p.tanggal DESC
        LIMIT ?, ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iii", $user_id, $start, $limit);
$stmt->execute();
$pengaduan = $stmt->get_result();
$stmt->close();

// Selanjutnya tinggal looping $pengaduan untuk tampilkan datanya
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
            <?php $no = $start + 1; while ($row = $pengaduan->fetch_assoc()): ?>
                <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['kode_aduan']) ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['kontak']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['isi_pengaduan'])) ?></td>
                <td>
                    <?php
                        $status = $row['status'];
                        $badgeClass = match($status) {
                        'Menunggu' => 'badge bg-secondary',
                        'Diproses' => 'badge bg-warning text-dark',
                        'Selesai'  => 'badge bg-success',
                        default    => 'badge bg-light text-dark'
                        };
                    ?>
                    <span class="<?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
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
        <div class="alert alert-info">Belum ada pengaduan yang dikirimkan.</div>
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