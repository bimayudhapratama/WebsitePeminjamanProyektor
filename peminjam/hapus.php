<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// (OPSIONAL) CEK APAKAH PEMINJAM PERNAH MEMINJAM
$cek = mysqli_prepare($connection,
    "SELECT COUNT(*) FROM peminjaman_proyektor WHERE id_peminjam = ?"
);
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
mysqli_stmt_bind_result($cek, $total);
mysqli_stmt_fetch($cek);
mysqli_stmt_close($cek);

if ($total > 0) {
    $_SESSION['error'] = "Peminjam tidak bisa dihapus karena masih memiliki riwayat peminjaman.";
    header("Location: index.php");
    exit;
}

// Hapus data
$stmt = mysqli_prepare($connection,
    "DELETE FROM peminjam WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: index.php");
exit;
