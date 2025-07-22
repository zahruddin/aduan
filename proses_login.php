<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: admin_dashboard.php");
  exit;
}

require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ambil data user beserta rolenya
    $stmt = $koneksi->prepare("SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $user_name, $hashed_password, $nama_lengkap, $role);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        // Simpan data ke session
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $user_name;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['role'] = $role;

        // Redirect berdasarkan role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: pengaduan.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: login.php");
        exit;
    }
}
?>
