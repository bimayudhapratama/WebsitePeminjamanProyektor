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

// Ambil data peminjam
$stmt = mysqli_prepare($connection,
    "SELECT nik, nama, instansi, foto_ktp FROM peminjam WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php");
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik      = trim($_POST['nik']);
    $nama     = trim($_POST['nama']);
    $instansi = trim($_POST['instansi']);
    $fotoBaru = $data['foto_ktp']; // default foto lama

    if ($nik === '' || $nama === '' || $instansi === '') {
        $error = "Semua field wajib diisi.";
    } else {

        // 🔹 jika upload foto baru
        if (!empty($_FILES['foto_ktp']['name'])) {

            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['foto_ktp']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "Format foto harus JPG, PNG, atau WEBP.";
            } elseif ($_FILES['foto_ktp']['size'] > 2 * 1024 * 1024) {
                $error = "Ukuran foto maksimal 2MB.";
            } else {
                $fotoBaru = uniqid('ktp_') . '.' . $ext;
                $target   = "../assets/img/ktp/" . $fotoBaru;

                move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $target);

                // hapus foto lama
                if (!empty($data['foto_ktp']) && file_exists("../assets/img/ktp/".$data['foto_ktp'])) {
                    unlink("../assets/img/ktp/".$data['foto_ktp']);
                }
            }
        }

        if (!$error) {
            $u = mysqli_prepare($connection,
                "UPDATE peminjam 
                 SET nik = ?, nama = ?, instansi = ?, foto_ktp = ?
                 WHERE id = ?"
            );
            mysqli_stmt_bind_param($u, "ssssi", $nik, $nama, $instansi, $fotoBaru, $id);
            mysqli_stmt_execute($u);
            mysqli_stmt_close($u);

            $success = "Data peminjam berhasil diperbarui.";
        }
    }
}

?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>

<div class="card-body" style="margin-left:50px;">
<h2>Edit Peminjam</h2>

<?= $error ? showAlert($error, 'danger') : '' ?>
<?= $success ? showAlert($success, 'success') : '' ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
    <label class="form-label">Foto KTP</label><br>

    <?php if (!empty($data['foto_ktp'])): ?>
        <img src="../assets/img/ktp/<?= htmlspecialchars($data['foto_ktp']) ?>"
             style="max-width:150px; display:block; margin-bottom:10px;">
    <?php endif; ?>

    <input type="file"
           name="foto_ktp"
           class="form-control"
           accept="image/*">
    <small class="text-muted">
        Kosongkan jika tidak ingin mengganti foto
    </small>
</div>


    <div class="mb-3">
        <label class="form-label">NIK</label>
        <input type="text" name="nik" class="form-control"
               value="<?= htmlspecialchars($data['nik']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control"
               value="<?= htmlspecialchars($data['nama']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Instansi</label>
        <input type="text" name="instansi" class="form-control"
               value="<?= htmlspecialchars($data['instansi']) ?>" required>
    </div>

    <button class="btn btn-primary">Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
</div>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
