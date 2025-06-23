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

    $stmt = $koneksi->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $user_name, $hashed_password, $nama_lengkap);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $user_name;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: login.php");
        exit;
    }
}
?>
