<?php
// $host = "localhost";
// $user = "seefanmy_aduan";
// $pass = "v8,sB6tu@a1~luw)";
// $db   = "seefanmy_aduan";
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pengaduan_masyarakat";

$koneksi = new mysqli($host, $user, $pass, $db);
if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
