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

  <!-- Header -->
  <header class="bg-merah text-white py-4 shadow-sm">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <a href="index.php">
          <img src="logo.png" alt="Logo Kecamatan" class="me-2" style="height: 40px;" />
          <h1 class="h4 mb-0">Layanan Pengaduan</h1>
        </a>
      </div>
      <nav class="mt-3 mt-md-0">
        <a href="formaduan.php" class="btn btn-outline-light me-2">Form Pengaduan</a>
        <a href="login.php" class="btn btn-light text-dark">Login Admin</a> 
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container my-5 flex-grow-1">
    <div class="card shadow mx-auto" style="max-width: 600px;">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Formulir Pengaduan</h2>
        <form id="complaintForm" novalidate>
          <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Anda" required>
            <div id="name-error" class="text-danger small mt-1 d-none">Nama wajib diisi.</div>
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Email / No. WhatsApp</label>
            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contoh: 081234567890" required>
            <div id="contact-error" class="text-danger small mt-1 d-none">Kontak wajib diisi.</div>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Jenis Pengaduan</label>
            <select class="form-select" id="type" name="type" required>
              <option value="" disabled selected>Pilih jenis pengaduan</option>
              <option>Pelayanan Administrasi</option>
              <option>Fasilitas Umum</option>
              <option>Keamanan / Ketertiban</option>
              <option>Lainnya</option>
            </select>
            <div id="type-error" class="text-danger small mt-1 d-none">Jenis pengaduan wajib dipilih.</div>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Isi Pengaduan</label>
            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Tuliskan pengaduan Anda di sini..." required></textarea>
            <div id="message-error" class="text-danger small mt-1 d-none">Isi pengaduan wajib diisi.</div>
          </div>
          <button type="submit" class="btn btn-merah w-100">Kirim Pengaduan</button>
        </form>

        <div id="successMessage" role="alert" class="alert alert-success text-center mt-4 d-none">
          Pengaduan Anda berhasil dikirim. Terima kasih!
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-secondary text-white text-center py-4 mt-5">
    <p class="fw-bold">Kontak Kecamatan</p>
    <p>Jl. Raya Sokosari No. 231, Soko Tuban</p>
    <p>Telepon: 021-123456 | WhatsApp: 0812-3456-7890</p>
    <p>Email: pengaduan@kec-sejahtera.go.id</p>
    <p class="text-light small mt-2">&copy; 2025 Kecamatan Soko</p>
  </footer>

  <!-- Bootstrap JS (opsional jika tidak pakai komponen JS-nya) -->
  <script>
    function validateField(field, errorId) {
      const errorElement = document.getElementById(errorId);
      if (!field.value.trim()) {
        errorElement.classList.remove('d-none');
        return false;
      } else {
        errorElement.classList.add('d-none');
        return true;
      }
    }

    document.getElementById('complaintForm').addEventListener('submit', function(event) {
      event.preventDefault();

      const nameField = this.elements['name'];
      const contactField = this.elements['contact'];
      const typeField = this.elements['type'];
      const messageField = this.elements['message'];

      const isNameValid = validateField(nameField, 'name-error');
      const isContactValid = validateField(contactField, 'contact-error');
      const isTypeValid = validateField(typeField, 'type-error');
      const isMessageValid = validateField(messageField, 'message-error');

      if (!isNameValid || !isContactValid || !isTypeValid || !isMessageValid) {
        return;
      }

      const complaint = {
        name: nameField.value.trim(),
        contact: contactField.value.trim(),
        type: typeField.value,
        message: messageField.value.trim(),
        timestamp: new Date().toISOString()
      };

      const complaints = JSON.parse(localStorage.getItem('complaints')) || [];
      complaints.push(complaint);
      localStorage.setItem('complaints', JSON.stringify(complaints));

      this.reset();

      const successMessage = document.getElementById('successMessage');
      successMessage.classList.remove('d-none');
      successMessage.focus();

      setTimeout(() => {
        successMessage.classList.add('d-none');
      }, 4000);
    });
  </script>
</body>
</html>
