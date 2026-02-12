<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('sewaProyektor');
require_once '../config/database.php';

// Ambil semua proyektor tersedia
$proyektorList = mysqli_query(
    $connection,
    "SELECT id, merk, foto FROM stokproyektor WHERE stok > 0 ORDER BY merk ASC"
);

$peminjamList = mysqli_query(
    $connection,
    "SELECT id, nik, nama, instansi FROM peminjam ORDER BY nama ASC"
);


$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode_proyektor = (int)($_POST['kode_proyektor'] ?? 0);
    $id_peminjam    = (int)($_POST['id_peminjam'] ?? 0);
    $instansi       = trim($_POST['instansi'] ?? '');
    $status         = 'Dipinjam';
    $lama_pinjam = (int)($_POST['lamapinjam'] ?? 0);

    if ($lama_pinjam <= 0) {
        $error = "Lama pinjam wajib diisi.";
    }




    if ($kode_proyektor <= 0) {
        $error = "Proyektor belum dipilih.";
    } elseif ($id_peminjam <= 0) {
        $error = "Peminjam belum dipilih.";
    } elseif ($instansi === '') {
        $error = "Instansi wajib diisi.";
    }

    if (!$error) {
        mysqli_begin_transaction($connection);

        try {
            // Ambil merk proyektor
            $q = mysqli_prepare($connection,
                "SELECT merk FROM stokproyektor WHERE id = ? LIMIT 1"
            );
            mysqli_stmt_bind_param($q, "i", $kode_proyektor);
            mysqli_stmt_execute($q);
            mysqli_stmt_bind_result($q, $merk);
            mysqli_stmt_fetch($q);
            mysqli_stmt_close($q);

            if (!$merk) {
                throw new Exception("Data proyektor tidak ditemukan.");
            }

            // INSERT peminjaman (INI YANG BENAR)
            $stmt = mysqli_prepare($connection,
                "INSERT INTO peminjaman_proyektor
(id_peminjam, kode_proyektor, instansi, tanggal_pinjam, merk_proyektor, status_peminjaman, lama_pinjam)
VALUES (?, ?, ?, NOW(), ?, ?, ?)
"
            );
mysqli_stmt_bind_param(
    $stmt,
    "iisssi",
    $id_peminjam,
    $kode_proyektor,
    $instansi,
    $merk,
    $status,
    $lama_pinjam
);


            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Kurangi stok
            $u = mysqli_prepare($connection,
                "UPDATE stokproyektor SET stok = stok - 1 WHERE id = ? AND stok > 0"
            );
            mysqli_stmt_bind_param($u, "i", $kode_proyektor);
            mysqli_stmt_execute($u);

            if (mysqli_stmt_affected_rows($u) === 0) {
                throw new Exception("Stok tidak mencukupi.");
            }
            mysqli_stmt_close($u);

            mysqli_commit($connection);
            $success = "Peminjaman berhasil ditambahkan.";

        } catch (Exception $e) {
            mysqli_rollback($connection);
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
        margin-left: 20px;
    }
</style>

<div class="card-body">
<h2>Tambah Peminjaman Proyektor</h2>

<?= $error ? showAlert($error, 'danger') : '' ?>
<?= $success ? showAlert($success, 'success') : '' ?>
<form method="POST">

<div class="mb-3 text-center">
    <img id="previewFoto"
         style="max-width:200px; display:none"
         class="rounded shadow">
</div>

<div class="mb-3">
    <label class="form-label">Kode Proyektor</label>
    <input type="number"
           name="kode_proyektor"
           id="kode_proyektor"
           class="form-control"
           readonly>
</div>

<div class="mb-3">
    <label class="form-label">Merk Proyektor</label>
    <select name="merkproyektor"
            id="merkproyektor"
            class="form-select"
            required>
        <option value="">-- Pilih Proyektor --</option>
        <?php while ($p = mysqli_fetch_assoc($proyektorList)): ?>
            <option value="<?= $p['id'] ?>"
                    data-foto="<?= htmlspecialchars($p['foto']) ?>">
                <?= htmlspecialchars($p['merk']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Nama Peminjam</label>
    <select name="id_peminjam"
            id="id_peminjam"
            class="form-select"
            required>
        <option value="">-- Pilih Peminjam --</option>
        <?php while ($row = mysqli_fetch_assoc($peminjamList)): ?>
            <option value="<?= $row['id'] ?>"
                    data-instansi="<?= htmlspecialchars($row['instansi']) ?>">
                <?= htmlspecialchars($row['nama']) ?>
                (<?= htmlspecialchars($row['nik']) ?>)
            </option>
        <?php endwhile; ?>
    </select>
</div>



<div class="mb-3">
    <label class="form-label">Instansi / Alamat</label>
    <input type="text"
           name="instansi"
           id="instansi"
           class="form-control"
           readonly
           required>
</div>
<div class="mb-3">
    <label class="form-label">Lama dipinjam</label>
    <input type="number"
       name="lamapinjam"
       id="lama_pinjam"
       class="form-control"
       placeholder="PerHari"
       required>

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
<script>
document.getElementById('id_peminjam').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const instansi = opt.dataset.instansi || '';
    document.getElementById('instansi').value = instansi;
});
</script>


<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>