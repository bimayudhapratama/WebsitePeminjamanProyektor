<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';

$isAdmin  = ($_SESSION['role'] === 'admin');
$username = $_SESSION['username'];

// Filter untuk penyewa
$where = '';
if (!$isAdmin) {
    $where = "WHERE p.nama_peminjam = ?";
}

// Query peminjaman
$sql = "
SELECT 
    p.id,
    p.kode_proyektor,
    pm.nama AS nama_peminjam,
    pm.nik,
    p.instansi,
    p.tanggal_pinjam,
    p.tanggal_kembali,
    p.merk_proyektor,
    p.status_peminjaman,
    p.lama_pinjam,
    s.harga,

    -- tanggal seharusnya kembali
    DATE_ADD(p.tanggal_pinjam, INTERVAL p.lama_pinjam DAY) AS batas_kembali,

    -- hari keterlambatan
    GREATEST(
        DATEDIFF(
            IFNULL(p.tanggal_kembali, CURDATE()),
            DATE_ADD(p.tanggal_pinjam, INTERVAL p.lama_pinjam DAY)
        ),
        0
    ) AS hari_terlambat,

    -- denda
    (
        GREATEST(
            DATEDIFF(
                IFNULL(p.tanggal_kembali, CURDATE()),
                DATE_ADD(p.tanggal_pinjam, INTERVAL p.lama_pinjam DAY)
            ),
            0
        ) * s.harga
    ) AS denda

FROM peminjaman_proyektor p
JOIN stokproyektor s ON p.kode_proyektor = s.id
JOIN peminjam pm ON p.id_peminjam = pm.id
$where
ORDER BY p.id DESC
";


$stmt = mysqli_prepare($connection, $sql);
if (!$isAdmin) {
    mysqli_stmt_bind_param($stmt, "s", $username);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="container-fluid" id="bh">
    <div class="mb-3" id="s">


        <style>
            #s {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
                padding: 20px;
                width: 100%;
                border: 2px solid #252525;
            }

            .card {
                width: 220px;
                border: 1px solid #ccc;
                border-radius: 8px;
                overflow: hidden;
                text-align: center;
                padding: 10px;
            }

            .card img {
                width: 100%;
                height: 140px;
                object-fit: cover;
            }

            .card button {
                margin-top: 10px;
                width: 100%;
            }
        </style>


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Daftar Peminjaman Proyektor</h2>

        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Atas Nama</th>
                        <th>Instansi / Alamat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Merk</th>
                        <th>Lama Pinjam</th>
                        <th>Denda</th>

                        <!-- <th>Total</th> -->
                        <th>Status</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['kode_proyektor']) ?></td>
                        <td>
                            <p><?= htmlspecialchars($row['nama_peminjam']) ?> (<?= htmlspecialchars($row['nik']) ?>)</p>
                        </td>
                        <td><?= htmlspecialchars($row['instansi']) ?></td>
                        <td><?= $row['tanggal_pinjam'] ?: '-' ?></td>
                        <td><?= $row['tanggal_kembali'] ?: '-' ?></td>
                        <td><?= htmlspecialchars($row['merk_proyektor']) ?></td>

                        <!-- TOTAL -->
                        <td>
                            <?= $row['lama_pinjam'] ?> hari<br>
                            <small class="text-muted">
                                Batas: <?= date('Y-m-d', strtotime($row['batas_kembali'])) ?>
                            </small>
                        </td>

                        <td>
                            <?php if ($row['hari_terlambat'] > 0): ?>
                            <span class="text-danger fw-bold">
                                Rp <?= number_format($row['denda'], 0, ',', '.') ?>
                            </span><br>
                            <small class="text-danger">
                                (<?= $row['hari_terlambat'] ?> hari telat)
                            </small>
                            <?php else: ?>
                            <span class="text-success">Rp 0</span>
                            <?php endif; ?>
                        </td>


                        <!-- <td>
                        Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?><br>
                        <small>(<?= $row['lama_hari'] ?> jam × Rp <?= number_format($row['harga'],0,',','.') ?>)</small>

                    </td> -->

                        <!-- STATUS -->
                        <td>
                            <?php if ($row['hari_terlambat'] > 0 && $row['status_peminjaman'] === 'Dipinjam'): ?>
                            <span class="badge bg-danger">Terlambat</span>
                            <?php else: ?>
                            <span
                                class="badge <?= $row['status_peminjaman'] === 'Dipinjam' ? 'bg-warning' : 'bg-success' ?>">
                                <?= htmlspecialchars($row['status_peminjaman']) ?>
                            </span>
                            <?php endif; ?>
                        </td>

                        <?php
                        // Gunakan variabel baru untuk query stok proyektor
                        $stokResult = mysqli_query(
                            $connection,
                            "SELECT * FROM stokproyektor WHERE id = " . (int)$row['kode_proyektor']
                        );

                        if (mysqli_num_rows($stokResult) > 0):
                            $stokRow = mysqli_fetch_assoc($stokResult);
                        ?>
                        <!-- AKSI -->
                        <td>
                            <img src="../assets/img/upload/<?= htmlspecialchars($stokRow['foto']) ?>"
                                alt="Foto Proyektor" style="max-width: 100px;">
                        </td>
                        <td>
                            <!-- STRUK: SELALU ADA -->
                            <a href="struk.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-info mb-1">
                                Cetak
                            </a>

                            <?php if ($isAdmin): ?>

                            <a href="edit.php?id=<?= $row['kode_proyektor'] ?>"
                                class="btn btn-sm btn-warning btn-edit mb-1">
                                Edit
                            </a>

                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete mb-1">
                                Hapus
                            </a>

                            <?php if ($row['status_peminjaman'] === 'Dipinjam'): ?>
                            <button type="button" class="btn btn-sm btn-success btn-kembalikan mb-1"
                                data-id="<?= $row['id'] ?>" data-tanggal="<?= $row['tanggal_kembali'] ?>">
                                Kembalikan
                            </button>
                            <?php endif; ?>

                            <?php endif; ?>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">Belum ada data peminjaman proyektor.</div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault(); // cegah navigasi default
                const href = this.href; // simpan URL

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href; // baru arahkan
                    }
                });
            });
        });

        document.querySelectorAll('.btn-kembalikan').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const tanggal = this.dataset.tanggal || 'sekarang';

                Swal.fire({
                    title: 'Kembalikan Proyektor?',
                    text: `Data akan dikembalikan pada tanggal ${tanggal}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kembalikan!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        // arahkan ke halaman PHP untuk update
                        window.location.href = `kembalikan.php?id=${id}`;
                    }
                });
            });
        });

        // Opsional: Edit juga bisa pakai alert konfirmasi
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault(); // cegah langsung navigasi
                const href = this.href;
                Swal.fire({
                    title: 'Edit Proyektor?',
                    text: 'Anda akan diarahkan ke halaman edit',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, edit',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>

    <?php include '../views/'.$THEME.'/lower_block.php'; ?>
    <?php include '../views/'.$THEME.'/footer.php'; ?>