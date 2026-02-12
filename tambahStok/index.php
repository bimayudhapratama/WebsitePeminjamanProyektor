<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('sewaPS');

require_once '../config/database.php';

            


$error = $success = '';

$foto_filename = '';
define('UPLOAD_DIR_PS', __DIR__ . '/../assets/img/upload/');
$kode_buku = generateNumericPSId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $merk_post  = trim($_POST['merk'] ?? '');
    $stok_post  = (int)($_POST['stok'] ?? 0);
    $harga_post = (int)($_POST['harga'] ?? 0);

    if ($merk_post === '') {
        $error = "Merk proyektor wajib diisi.";
    }

    /* Upload foto */
    $foto_filename = 'default.jpg';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_file_upload_PS($_FILES['foto']);
        if ($upload_result === false) {
            $error = "Upload foto gagal (maks 2MB, JPG/PNG/WebP)";
        } else {
            $foto_filename = $upload_result;
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO stokproyektor (merk, stok, harga, foto)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "siis",
            $merk_post,
            $stok_post,
            $harga_post,
            $foto_filename
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Data proyektor berhasil ditambahkan.";
                        echo "
            <script>
                Swal.fire({
    title: 'Berhasil!',
    text: 'Data penyewaan PS berhasil ditambahkan',
    icon: 'success',
    timer: 2000,
    showConfirmButton: false
}).then(() => {
    window.location.href = 'index.php';
});

            </script>";
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($connection);
        }   }
}

?>
<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<!-- <?php include '../views/'.$THEME.'/topnav.php'; ?> -->
<?php include '../views/'.$THEME.'/upper_block.php'; ?>


<h2 style="margin: 0px 20px 0px 20px;">Tambah Stok Proyektor</h2>

<?php if ($error): ?>
<?= showAlert($error, 'danger') ?>
<?php endif; ?>

<?php if ($success): ?>
<?= showAlert($success, 'success') ?>
<a href="index.php" class="btn btn-secondary">Kembali</a>
<?php else: ?>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<form method="POST" enctype="multipart/form-data" style="margin: 0px 20px 0px 20px;">

    <!-- Jenis PS -->
    <div class="mb-3">
        <label class="form-label">Merk Proyektor</label>
        <input type="text" name="merk" class="form-select" id="">
    </div>

    <!-- Harga -->
    <div class="mb-3">
        <label class="form-label">Harga Per Jam</label>
        <input type="number" name="harga" class="form-control" required>
    </div>

    <!-- Total Unit -->
    <div class="mb-3">
        <label class="form-label">Total Unit</label>
        <input type="number" name="stok" class="form-control" required>
    </div>

    <!-- Foto -->
    <div class="mb-3">
        <label class="form-label">Foto Proyektor</label>
        <input type="file" name="foto" class="form-control" style="padding: 10px 0px 0px 20px; height:50px;" onchange="previewImage(event)">
        <img id="imagePreview" style="display:none; margin-top:10px; max-width:200px;">
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>

</form>


<?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<!-- Add JavaScript for image preview -->
<script>
    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('imagePreview');
        reader.onload = function () {
            preview.src = reader.result;
            preview.style.display = 'block';
        }
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            preview.style.display = 'none';
            preview.src = '#';
        }
    }
    // Optional: Form validation for file size
    document.querySelector('form').addEventListener('submit', function (e) {
        const fileInput = document.getElementById('fotoInput');
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
            if (fileSize > 2) {
                e.preventDefault();
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                return false;
            }
        }
    });
</script>
<?php include '../views/'.$THEME.'/footer.php'; ?>