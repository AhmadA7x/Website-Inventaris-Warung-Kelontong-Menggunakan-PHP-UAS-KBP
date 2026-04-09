<?php
require_once 'koneksi.php';

function bersihkan_input($koneksi, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

function ambil_semua_barang($koneksi, $keyword = '') {
    if ($keyword != '') {
        $keyword = bersihkan_input($koneksi, $keyword);
        $query = "SELECT * FROM barang 
                  WHERE nama_barang LIKE '%$keyword%' 
                     OR kategori LIKE '%$keyword%' 
                     OR keterangan LIKE '%$keyword%'
                  ORDER BY id DESC";
    } else {
        $query = "SELECT * FROM barang ORDER BY id DESC";
    }

    $result = mysqli_query($koneksi, $query);

    $data = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    return $data;
}

function ambil_barang_by_id($koneksi, $id) {
    $id = (int) $id;
    $query = "SELECT * FROM barang WHERE id = $id";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

function tambah_barang($koneksi, $data) {
    $nama_barang   = bersihkan_input($koneksi, $data['nama_barang']);
    $kategori      = bersihkan_input($koneksi, $data['kategori']);
    $jumlah        = (int) $data['jumlah'];
    $stok_minimum  = (int) $data['stok_minimum'];
    $tanggal_masuk = bersihkan_input($koneksi, $data['tanggal_masuk']);
    $keterangan    = bersihkan_input($koneksi, $data['keterangan']);

    $query = "INSERT INTO barang (nama_barang, kategori, jumlah, stok_minimum, tanggal_masuk, keterangan)
              VALUES ('$nama_barang', '$kategori', $jumlah, $stok_minimum, '$tanggal_masuk', '$keterangan')";

    return mysqli_query($koneksi, $query);
}

function update_barang($koneksi, $data) {
    $id            = (int) $data['id'];
    $nama_barang   = bersihkan_input($koneksi, $data['nama_barang']);
    $kategori      = bersihkan_input($koneksi, $data['kategori']);
    $jumlah        = (int) $data['jumlah'];
    $stok_minimum  = (int) $data['stok_minimum'];
    $tanggal_masuk = bersihkan_input($koneksi, $data['tanggal_masuk']);
    $keterangan    = bersihkan_input($koneksi, $data['keterangan']);

    $query = "UPDATE barang SET 
                nama_barang = '$nama_barang',
                kategori = '$kategori',
                jumlah = $jumlah,
                stok_minimum = $stok_minimum,
                tanggal_masuk = '$tanggal_masuk',
                keterangan = '$keterangan'
              WHERE id = $id";

    return mysqli_query($koneksi, $query);
}

function hapus_barang($koneksi, $id) {
    $id = (int) $id;
    $query = "DELETE FROM barang WHERE id = $id";
    return mysqli_query($koneksi, $query);
}

function hitung_barang($koneksi) {
    $query = "SELECT COUNT(*) as total FROM barang";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

function hitung_total_unit($koneksi) {
    $query = "SELECT COALESCE(SUM(jumlah), 0) as total_unit FROM barang";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total_unit'];
}

function hitung_stok_aman($koneksi) {
    $query = "SELECT COUNT(*) as total FROM barang WHERE jumlah >= stok_minimum";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

function hitung_stok_menipis($koneksi) {
    $query = "SELECT COUNT(*) as total FROM barang WHERE jumlah < stok_minimum";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

function format_tanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    ];

    $pecah = explode('-', $tanggal);

    if (count($pecah) == 3) {
        $tgl = (int) $pecah[2];
        $bln = (int) $pecah[1];
        $thn = $pecah[0];
        return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
    }

    return $tanggal;
}

function badge_stok($jumlah, $stok_minimum) {
    if ($jumlah == 0) {
        return 'bg-danger';
    } elseif ($jumlah < $stok_minimum) {
        return 'bg-warning text-dark';
    } else {
        return 'bg-success';
    }
}

function label_stok($jumlah, $stok_minimum) {
    if ($jumlah == 0) {
        return 'Habis';
    } elseif ($jumlah < $stok_minimum) {
        return 'Menipis';
    } else {
        return 'Aman';
    }
}

function badge_kategori($kategori) {
    switch ($kategori) {
        case 'Minuman':
            return 'bg-primary';
        case 'Makanan Ringan':
            return 'bg-info text-dark';
        case 'Sembako':
            return 'bg-warning text-dark';
        case 'Rokok & Tembakau':
            return 'bg-secondary';
        case 'Kebersihan & Perawatan':
            return 'bg-success';
        case 'Lainnya':
            return 'bg-dark';
        default:
            return 'bg-secondary';
    }
}
