<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';

$stokList = mysqli_query(
    $connection,
    "SELECT * FROM stokproyektor ORDER BY id DESC"
);
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="container-fluid">

    <h4 class="mb-3">Daftar Stok Proyektor</h4>

    <?php if (mysqli_num_rows($stokList) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Merk / Model</th>
                        <th>Stok</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($stok = mysqli_fetch_assoc($stokList)): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td>
                                <img src="../assets/img/upload/<?= htmlspecialchars($stok['foto']) ?>"
                                     style="width:100px; height:70px; object-fit:cover;"
                                     class="rounded">
                            </td>

                            <td><?= htmlspecialchars($stok['merk']) ?></td>

                            <td>
                                <span class="badge <?= $stok['stok'] > 0 ? '' : 'bg-danger' ?>" style="font-size:1.5rem;">
                                    <?= (int)$stok['stok'] ?>
                                </span>
                            </td>

                            <td>
                                <a href="../sewaProyektor/edit.php?id=<?= $stok['id'] ?>"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            Belum ada stok proyektor.
        </div>
    <?php endif; ?>

</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
