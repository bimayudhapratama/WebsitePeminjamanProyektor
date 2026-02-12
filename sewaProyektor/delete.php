<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('sewaProyektor');

require_once '../config/database.php';

// Hanya admin yang bisa menghapus
if ($_SESSION['role'] !== 'admin') {
    die('Akses ditolak');
}

// Ambil ID
$id = (int) ($_GET['id'] ?? 0);

if ($id) {
    $stmt = mysqli_prepare($connection, "DELETE FROM `peminjaman_proyektor` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Redirect ke halaman daftar
header("Location: /pemrogramanweb/kelompok/sewaProyektor/index.php");
exit;
?>
