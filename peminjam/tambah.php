<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik      = trim($_POST['nik']);
    $nama     = trim($_POST['nama']);
    $instansi = trim($_POST['instansi']);

    if ($nik === '' || $nama === '') {
        $error = "NIK dan Nama wajib diisi.";
    } else {

        try {
            $fotoKtp = uploadKTP($_FILES['foto_ktp']);

            $stmt = mysqli_prepare($connection,
                "INSERT INTO peminjam (nik, nama, instansi, foto_ktp)
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $nik,
                $nama,
                $instansi,
                $fotoKtp
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php");
                exit;
            } else {
                $error = "NIK sudah terdaftar.";
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}



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
<h2>Tambah Peminjam</h2>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
    <label class="form-label">NIK KTP</label>
    <input type="text"
           name="nik"
           class="form-control"
           maxlength="20"
           required
           placeholder="Contoh: 3201xxxxxxxxxxxx">
</div>

<div class="mb-3">
    <label class="form-label">Foto KTP</label>
    <input type="file"
           name="foto_ktp"
           class="form-control"
           accept="image/jpeg,image/png"
           required>
    <small class="text-muted">
        Format JPG / PNG, max 2MB
    </small>
</div>


<div class="mb-3">
    <label class="form-label">Nama Peminjam</label>
    <input type="text"
           name="nama"
           class="form-control"
           required>
</div>

<div class="mb-3">
    <label class="form-label">Instansi / Alamat</label>
    <input type="text"
           name="instansi"
           class="form-control">
</div>

<button class="btn btn-primary">Simpan</button>
<a href="index.php" class="btn btn-secondary">Batal</a>

</form>

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