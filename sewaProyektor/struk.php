<?php
session_start();
require_once '../config/database.php';
require_once '../lib/auth.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);

$sql = "
SELECT 
    p.id,
    pm.nama AS nama_peminjam,
    pm.instansi,
    p.tanggal_pinjam,
    p.tanggal_kembali,
    p.status_peminjaman,
    p.lama_pinjam,
    s.merk AS merk_proyektor,
    s.harga,

    -- lama aktual (hari)
    GREATEST(
        CEIL(
            TIMESTAMPDIFF(
                HOUR,
                p.tanggal_pinjam,
                IFNULL(p.tanggal_kembali, NOW())
            ) / 24
        ),
        1
    ) AS lama_aktual,

    -- hari terlambat
    GREATEST(
        CEIL(
            TIMESTAMPDIFF(
                HOUR,
                p.tanggal_pinjam,
                IFNULL(p.tanggal_kembali, NOW())
            ) / 24
        ) - p.lama_pinjam,
        0
    ) AS hari_terlambat,

    -- total denda (hari × harga)
    GREATEST(
        CEIL(
            TIMESTAMPDIFF(
                HOUR,
                p.tanggal_pinjam,
                IFNULL(p.tanggal_kembali, NOW())
            ) / 24
        ) - p.lama_pinjam,
        0
    ) * s.harga AS total_denda

FROM peminjaman_proyektor p
JOIN stokproyektor s ON p.kode_proyektor = s.id
JOIN peminjam pm ON p.id_peminjam = pm.id
WHERE p.id = ?;

";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Peminjaman</title>
<style>
body {
    font-family: monospace;
    background: #f5f5f5;
}
.struk {
    width: 360px;
    background: #fff;
    margin: 20px auto;
    padding: 16px;
    border: 1px dashed #333;
}
h2 {
    text-align: center;
    margin: 0;
}
.center { text-align: center; }
hr {
    border: none;
    border-top: 1px dashed #333;
    margin: 8px 0;
}
table {
    width: 100%;
    font-size: 14px;
}
td {
    padding: 3px 0;
    vertical-align: top;
}
.right { text-align: right; }
.total {
    font-weight: bold;
    font-size: 15px;
}
.status {
    text-align: center;
    font-weight: bold;
    margin-top: 6px;
}
.btn-print {
    text-align: center;
    margin-top: 12px;
}
@media print {
    .btn-print { display: none; }
    body { background: none; }
}
</style>
</head>

<body>

<div class="struk">
    <h2>STRUK PEMINJAMAN</h2>
    <p class="center">Rental Proyektor</p>

    <hr>

    <table>
        <tr>
            <td>ID Transaksi</td>
            <td class="right">#<?= $data['id'] ?></td>
        </tr>
        <tr>
            <td>Nama</td>
            <td class="right"><?= htmlspecialchars($data['nama_peminjam'] ?? '')?></td>
        </tr>
        <tr>
            <td>Instansi</td>
            <td class="right"><?= htmlspecialchars($data['instansi'] ?? '') ?></td>
        </tr>
        <tr>
            <td>Merk</td>
            <td class="right"><?= htmlspecialchars($data['merk_proyektor']?? '')?></td>
        </tr>
        <tr>
            <td>Pinjam</td>
            <td class="right"><?= $data['tanggal_pinjam'] ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="right"><?= $data['tanggal_kembali'] ?: '-' ?></td>
        </tr>
    </table>

    <hr>

    <table>
<tr>
    <td>Lama Pinjam</td>
    <td class="right"><?= $data['lama_pinjam'] ?> Hari</td>
</tr>
<tr>
    <td>Lama Aktual</td>
    <td class="right"><?= $data['lama_aktual'] ?> Hari</td>
</tr>
<tr>
    <td>Terlambat</td>
    <td class="right"><?= $data['hari_terlambat'] ?> Hari</td>

</tr>

<tr class="total">
    <td>Total Denda</td>
    <td class="right">
        Rp <?= number_format($data['total_denda'], 0, ',', '.') ?>
    </td>
</tr>
</table>

    <hr>

    <div class="status">
        Status: <?= htmlspecialchars($data['status_peminjaman']?? '')?>
    </div>

    <p class="center">
        Terima kasih 🙏<br>
        Simpan struk ini sebagai bukti
    </p>

    <div class="btn-print">
        <button onclick="window.print()">🖨 Cetak Struk</button>
    </div>
</div>

</body>
</html>
