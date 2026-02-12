<?php
session_start();

require_once '../lib/functions.php';
require_once '../lib/auth.php';
require_once '../config/database.php';

// cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak");
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID tidak valid");
}

mysqli_begin_transaction($connection);

try {
    // ambil kode proyektor
    $q = mysqli_prepare($connection,
        "SELECT kode_proyektor FROM peminjaman_proyektor WHERE id = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($q, "i", $id);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $kode_proyektor);
    mysqli_stmt_fetch($q);
    mysqli_stmt_close($q);

    if (!$kode_proyektor) {
        throw new Exception("Data peminjaman tidak ditemukan");
    }

    // update peminjaman (JANGAN ubah lama_pinjam)
    $stmt = mysqli_prepare($connection,
        "UPDATE peminjaman_proyektor
         SET tanggal_kembali = NOW(),
             status_peminjaman = 'Dikembalikan'
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // kembalikan stok
    $u = mysqli_prepare($connection,
        "UPDATE stokproyektor
         SET stok = stok + 1
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($u, "i", $kode_proyektor);
    mysqli_stmt_execute($u);
    mysqli_stmt_close($u);

    mysqli_commit($connection);

    header("Location: index.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($connection);
    die("Gagal mengembalikan proyektor: " . $e->getMessage());
}
