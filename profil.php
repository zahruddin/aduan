<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

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
        <a href="admin_dashboard.php" class="btn btn-light text-dark">Dashboard</a>
        <a href="logout.php" class="btn btn-outline-light text-light">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Konten -->
<main class="container my-5 flex-grow-1">
  <h2 class="mb-4">Kelola Profil</h2>

  <?php
    $user_id = $_SESSION['user_id'];

    // Ambil data user dari database
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Edit Profil
    if (isset($_POST['update_profil'])) {
      $nama_lengkap = trim($_POST['nama_lengkap']);
      $username = trim($_POST['username']);

      if (!empty($nama_lengkap) && !empty($username)) {
        $stmt = $koneksi->prepare("UPDATE users SET nama_lengkap = ?, username = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama_lengkap, $username, $user_id);
        if ($stmt->execute()) {
          echo '<div class="alert alert-success">Profil berhasil diperbarui.</div>';
          $user['nama_lengkap'] = $nama_lengkap;
          $user['username'] = $username;
        } else {
          echo '<div class="alert alert-danger">Gagal memperbarui profil. Username mungkin sudah digunakan.</div>';
        }
      } else {
        echo '<div class="alert alert-warning">Nama lengkap dan username tidak boleh kosong.</div>';
      }
    }

    // Ubah Password
    if (isset($_POST['ubah_password'])) {
      $password_lama = $_POST['password_lama'];
      $password_baru = $_POST['password_baru'];
      $konfirmasi = $_POST['konfirmasi_password'];

      if (!empty($password_lama) && !empty($password_baru) && !empty($konfirmasi)) {
        if (!password_verify($password_lama, $user['password'])) {
          echo '<div class="alert alert-danger">Password lama salah.</div>';
        } elseif ($password_baru !== $konfirmasi) {
          echo '<div class="alert alert-warning">Konfirmasi password tidak cocok.</div>';
        } else {
          $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
          $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE id = ?");
          $stmt->bind_param("si", $password_hash, $user_id);
          if ($stmt->execute()) {
            echo '<div class="alert alert-success">Password berhasil diubah.</div>';
          } else {
            echo '<div class="alert alert-danger">Gagal mengubah password.</div>';
          }
        }
      } else {
        echo '<div class="alert alert-warning">Semua kolom password wajib diisi.</div>';
      }
    }
  ?>

  <!-- Form Edit Profil -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-merah text-white">
      <strong>Edit Profil</strong>
    </div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="update_profil" value="1">
        <div class="mb-3">
          <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>

  <!-- Form Ubah Password -->
  <div class="card shadow-sm">
    <div class="card-header bg-merah text-white">
      <strong>Ubah Password</strong>
    </div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="ubah_password" value="1">
        <div class="mb-3">
          <label for="password_lama" class="form-label">Password Lama</label>
          <input type="password" name="password_lama" id="password_lama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password_baru" class="form-label">Password Baru</label>
          <input type="password" name="password_baru" id="password_baru" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
          <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-secondary">Ubah Password</button>
      </form>
    </div>
  </div>
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