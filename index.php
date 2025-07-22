<?php
require 'koneksi.php';
$kategori = $koneksi->query("SELECT id, nama_kategori FROM kategori_pengaduan");
session_start();

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

  <!-- Hero / Landing Section -->
  <section class="bg-white py-5">
    <div class="container text-center">
      <h2 class="display-5 fw-bold mb-3">Selamat Datang di Layanan Pengaduan Kecamatan Soko</h2>
      <p class="lead mb-4">
        Sampaikan keluhan atau pengaduan Anda dengan mudah dan cepat. Kami siap membantu Anda dalam meningkatkan kualitas layanan publik di Kecamatan.
      </p>
      <a href="login.php" class="btn btn-merah btn-lg">Kirim Pengaduan Sekarang</a>
    </div>
  </section>

  <!-- Footer -->
  <?php include "footer.php"; ?>

  <!-- Validasi JavaScript -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
