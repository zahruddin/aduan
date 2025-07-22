<header class="bg-merah text-white py-4 shadow-sm">
  <div class="container d-flex flex-wrap justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <a href="index.php">
        <img src="logo.png" alt="Logo Kecamatan" class="me-2" style="height: 40px;" />
      </a>
      <h1 class="h4 mb-0">
        <?php
          if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo "Dashboard Admin";
          } else {
            echo "Layanan Pengaduan";
          }
        ?>
      </h1>
    </div>
    
    <nav class="mt-3 mt-md-0 d-flex align-items-center">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="me-3">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="admin_dashboard.php" class="btn btn-outline-light me-2">Dashboard Admin</a>
          <a href="profil.php" class="btn btn-outline-light text-light me-2">Kelola Profil</a>
        <?php else: ?>
          <a href="profil.php" class="btn btn-outline-light text-light me-2">Kelola Profil</a>
          <a href="status.php" class="btn btn-outline-light text-light me-2">Status Aduan</a>
          <a href="pengaduan.php" class="btn btn-outline-light me-2">Form Pengaduan</a>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-light text-dark">Logout</a>
      
      <?php else: ?>
        <!-- <a href="#form-pengaduan" class="btn btn-outline-light me-2">Form Pengaduan</a> -->
        <a href="login.php" class="btn btn-light text-dark">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
