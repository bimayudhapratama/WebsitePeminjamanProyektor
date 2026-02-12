<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';
$sql = "SELECT * FROM peminjam ORDER BY id DESC";
$result = mysqli_query($connection, $sql);


?>



<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<!-- <?php include '../views/'.$THEME.'/topnav.php'; ?> -->
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<script>
const selectMerk  = document.getElementById('merkproyektor');
const inputKode   = document.getElementById('kode_proyektor');
const previewFoto = document.getElementById('previewFoto');

selectMerk.addEventListener('change', () => {
    const opt  = selectMerk.options[selectMerk.selectedIndex];
    const id   = opt.value;
    const foto = opt.dataset.foto;

    if (id) {
        inputKode.value = id;
        previewFoto.src = "../assets/img/upload/" + foto;
        previewFoto.style.display = "block";
    } else {
        inputKode.value = "";
        previewFoto.style.display = "none";
    }
});
</script>



<style>
    .card-body {
        margin-left: 50px;
    }
</style>

<div class="card-body">
<h2>Data Peminjam</h2>

<a href="tambah.php" class="btn btn-primary mb-3">
    + Tambah Peminjam
</a>

<div class="table-responsive">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Foto KTP</th>
            <th>NIK KTP</th>
            <th>Nama</th>
            <th>Instansi</th>
            <th>Aksi</th>
        </tr>
    </thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>

        <!-- FOTO KTP -->
        <td>
            <?php if (!empty($row['foto_ktp'])): ?>
                <a href="../assets/img/ktp/<?= htmlspecialchars($row['foto_ktp']) ?>" target="_blank">
                    <img src="../assets/img/ktp/<?= htmlspecialchars($row['foto_ktp']) ?>"
                         alt="KTP"
                         style="width:80px; border-radius:4px;">
                </a>
            <?php else: ?>
                <span class="text-muted">Tidak ada</span>
            <?php endif; ?>
        </td>

        <!-- NIK -->
        <td><?= htmlspecialchars($row['nik']) ?></td>

        <!-- NAMA -->
        <td><?= htmlspecialchars($row['nama']) ?></td>

        <!-- INSTANSI -->
        <td><?= htmlspecialchars($row['instansi']) ?></td>

        <!-- AKSI -->
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                Edit
            </a>
            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete">
                Hapus
            </a>
        </td>
    </tr>
<?php endwhile; ?>
</tbody>

</table>
</div>

</div>

        <!-- <div class="mb-3">
                <label class="form-label">Merk Proyektor</label>
                <input type="text" name="merk_proyektor" class="form-control">
            </div> -->
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

    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const selectMerk  = document.getElementById('merkproyektor');
    const inputKode   = document.getElementById('kode_proyektor');
    const previewFoto = document.getElementById('previewFoto');

    selectMerk.addEventListener('change', () => {
        const opt  = selectMerk.options[selectMerk.selectedIndex];
        const id   = opt.value;
        const foto = opt.dataset.foto;

        if (id) {
            inputKode.value = id;
            previewFoto.src = "../assets/img/upload/" + foto;
            previewFoto.style.display = "block";
        } else {
            inputKode.value = "";
            previewFoto.style.display = "none";
        }
    });
});
</script>


<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>