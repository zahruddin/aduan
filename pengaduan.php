<?php
require 'koneksi.php';
$kategori = $koneksi->query("SELECT id, nama_kategori FROM kategori_pengaduan");
session_start();
// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  // exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Website Pengaduan Kecamatan</title>
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
    #successMessage {
      transition: opacity 0.5s ease-in-out;
    }
  </style>
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">

  <!-- Header / Navbar -->
  <?php include "header.php"; ?>

  <!-- Formulir Pengaduan -->
  
  <section id="form-pengaduan" class="container my-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
       <?php if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['kode'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Pengaduan berhasil dikirim! kode aduan anda adalah <?php echo $_GET['kode']; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
          </div>
        <?php endif; ?>

      <div class="card-body">
        <h2 class="card-title text-center mb-4">Formulir Pengaduan</h2>
        <form id="complaintForm" action="simpan_pengaduan.php" method="POST">
          <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Email / No. WhatsApp</label>
            <input type="text" class="form-control" id="contact" name="contact" required>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Jenis Pengaduan</label>
            <select class="form-select" id="type" name="type" required>
              <option value="" disabled selected>Pilih jenis pengaduan</option>
              <?php while ($row = $kategori->fetch_assoc()): ?>
                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_kategori']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Isi Pengaduan</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-merah w-100">Kirim Pengaduan</button>
        </form>


        <div id="successMessage" role="alert" class="alert alert-success text-center mt-4 d-none">
          Pengaduan Anda berhasil dikirim. Terima kasih!
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include "footer.php"; ?>

  <!-- Validasi JavaScript -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
