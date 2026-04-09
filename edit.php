<?php
require_once 'functions.php';

$error = '';
$barang = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $barang = ambil_barang_by_id($koneksi, $id);
    if ($barang == null) {
        header('Location: index.php?pesan=tidak_ditemukan');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id'            => $id,
        'nama_barang'   => $_POST['nama_barang'] ?? '',
        'kategori'      => $_POST['kategori'] ?? '',
        'jumlah'        => $_POST['jumlah'] ?? 0,
        'stok_minimum'  => $_POST['stok_minimum'] ?? 5,
        'tanggal_masuk' => $_POST['tanggal_masuk'] ?? '',
        'keterangan'    => $_POST['keterangan'] ?? ''
    ];

    if (empty($data['nama_barang']) || empty($data['kategori']) || empty($data['tanggal_masuk'])) {
        $error = 'Semua field yang bertanda (*) wajib diisi!';
        $barang = $data;
    } elseif ($data['jumlah'] < 0) {
        $error = 'Jumlah barang tidak boleh bernilai negatif!';
        $barang = $data;
    } elseif ($data['stok_minimum'] < 0) {
        $error = 'Stok minimum tidak boleh bernilai negatif!';
        $barang = $data;
    } else {
        $hasil = update_barang($koneksi, $data);
        if ($hasil) {
            header('Location: index.php?pesan=update_sukses');
            exit;
        } else {
            $error = 'Gagal memperbarui data. Error: ' . mysqli_error($koneksi);
        }
    }
}

$kategori_list = ['Minuman', 'Makanan Ringan', 'Sembako', 'Rokok & Tembakau', 'Kebersihan & Perawatan', 'Lainnya'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Inventaris Warung Kelontong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #1a1a2e; min-height: 100vh; }
        .navbar-custom { background: var(--primary-gradient); padding: 1rem 0; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3); }
        .navbar-custom .navbar-brand { font-weight: 800; font-size: 1.4rem; color: #fff !important; letter-spacing: -0.5px; }
        .navbar-custom .navbar-brand i { background: rgba(255,255,255,0.2); padding: 8px 10px; border-radius: 12px; margin-right: 10px; }
        .nav-link-custom { color: rgba(255,255,255,0.85) !important; font-weight: 500; padding: 8px 16px !important; border-radius: 10px; transition: var(--transition); }
        .nav-link-custom:hover, .nav-link-custom.active { color: #fff !important; background: rgba(255,255,255,0.15); }
        .page-header { background: var(--primary-gradient); padding: 20px 0 60px 0; margin-top: -1px; text-align: center; }
        .page-header h2 { font-weight: 800; color: #fff; font-size: 1.8rem; }
        .page-header p { color: rgba(255,255,255,0.8); }
        .edit-id-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.2); color: #fff; padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 8px; }
        .form-card { background: #fff; border-radius: var(--border-radius); box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.04); padding: 36px; margin-top: -40px; position: relative; z-index: 10; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #374151; margin-bottom: 6px; }
        .form-control, .form-select { border: 2px solid #e9ecef; border-radius: 12px; padding: 12px 16px; font-size: 0.9rem; transition: var(--transition); }
        .form-control:focus, .form-select:focus { border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .btn-primary-gradient { background: var(--primary-gradient); border: none; color: #fff; font-weight: 600; padding: 12px 32px; border-radius: 12px; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); color: #fff; }
        .btn-secondary-outline { border: 2px solid #e9ecef; background: #fff; color: #6b7280; font-weight: 600; padding: 12px 32px; border-radius: 12px; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-secondary-outline:hover { border-color: #667eea; color: #667eea; transform: translateY(-2px); }
        .custom-alert { border: none; border-radius: 12px; padding: 14px 20px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .footer { text-align: center; padding: 30px 0; color: #9ca3af; font-size: 0.85rem; }
        .form-text { font-size: 0.8rem; color: #9ca3af; margin-top: 4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-shop"></i>Inventaris Warung Kelontong</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255,255,255,0.3);">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="tambah.php"><i class="bi bi-plus-circle me-1"></i>Tambah Barang</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="page-header">
    <div class="container">
        <h2><i class="bi bi-pencil-square me-2"></i>Edit Data Barang</h2>
        <p>Perbarui informasi stok barang warung</p>
        <div class="edit-id-badge"><i class="bi bi-hash"></i>ID Barang: <?= $barang['id']; ?></div>
    </div>
</section>

<div class="container" style="max-width: 800px;">

    <?php if ($error != ''): ?>
        <div class="alert custom-alert alert-danger alert-dismissible fade show mb-3" style="margin-top: -40px; position: relative; z-index: 11;">
            <i class="bi bi-exclamation-circle-fill"></i><?= $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="edit.php?id=<?= $barang['id']; ?>" method="POST">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control"
                           value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?= htmlspecialchars($kat); ?>"
                                <?= ($barang['kategori'] == $kat) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" min="0"
                           value="<?= (int) $barang['jumlah']; ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimum" class="form-control" min="0"
                           value="<?= (int) $barang['stok_minimum']; ?>" required>
                    <div class="form-text">Batas peringatan stok menipis</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-control"
                           value="<?= htmlspecialchars($barang['tanggal_masuk']); ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"
                              placeholder="Contoh: harga beli, supplier, atau catatan lain (opsional)"><?= htmlspecialchars($barang['keterangan']); ?></textarea>
                </div>

                <div class="col-12 d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="bi bi-check-lg"></i>Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn-secondary-outline">
                        <i class="bi bi-arrow-left"></i>Kembali
                    </a>
                </div>

            </div>
        </form>
    </div>

    <div class="footer"><p>&copy; <?= date('Y'); ?> Inventaris Warung Kelontong</p></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
