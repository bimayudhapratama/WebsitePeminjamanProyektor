<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('sewaProyektor');

require_once '../config/database.php';
$foto_filename = '';
define('UPLOAD_DIR_PS', __DIR__ . '/../assets/img/upload/');
// $kode_buku = generateNumericPSId();

if ($_SESSION['role'] !== 'admin') {
    die('Akses ditolak');
}



$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');

/* Ambil data lama */
$stmt = mysqli_prepare(
    $connection,
    "SELECT id, merk, stok, harga, foto FROM stokproyektor WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$proyektor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$proyektor) {
    redirect('index.php');
}

$error = $success = '';

/* =======================
   PROSES UPDATE
======================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $merk_post  = trim($_POST['merk'] ?? '');
    $stok_post  = (int)($_POST['stok'] ?? 0);
    $harga_post = (int)($_POST['harga'] ?? 0);

    if ($merk_post === '') {
        $error = "Merk proyektor wajib diisi.";
    }

    /* Foto */
    $foto_filename = $proyektor['foto']; // default foto lama
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = handle_file_upload_PS($_FILES['foto']);
        if ($upload === false) {
            $error = "Upload foto gagal (maks 2MB | JPG/PNG/WebP)";
        } else {
            $foto_filename = $upload;
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare(
            $connection,
            "UPDATE stokproyektor
             SET merk = ?, stok = ?, harga = ?, foto = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "siisi",
            $merk_post,
            $stok_post,
            $harga_post,
            $foto_filename,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Stok proyektor berhasil diperbarui.";
            echo "<script>
                setTimeout(() => window.location.href = 'index.php', 1500);
            </script>";
        } else {
            $error = "Gagal memperbarui: " . mysqli_error($connection);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="card-body" style="margin-left:20px;">
    <h2>Edit Stok Proyektor</h2>

    <?php if ($error): ?>
        <?= showAlert($error, 'danger') ?>
    <?php endif; ?>

    <?php if ($success): ?>
        <?= showAlert($success, 'success') ?>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Merk Proyektor</label>
            <input type="text" name="merk" class="form-control"
                   value="<?= htmlspecialchars($proyektor['merk']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Jam</label>
            <input type="number" name="harga" class="form-control"
                   value="<?= (int)$proyektor['harga'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control"
                   value="<?= (int)$proyektor['stok'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Proyektor</label>
            <input type="file" name="foto" class="form-control"
                   onchange="previewImage(event)">
            <img id="preview"
                 src="../assets/img/upload/<?= htmlspecialchars($proyektor['foto']) ?>"
                 style="margin-top:10px;max-width:200px;">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>

<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    const reader = new FileReader();
    reader.onload = () => img.src = reader.result;
    reader.readAsDataURL(event.target.files[0]);
}
</script>
