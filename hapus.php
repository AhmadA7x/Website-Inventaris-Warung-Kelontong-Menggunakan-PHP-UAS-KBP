<?php
require_once 'functions.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $barang = ambil_barang_by_id($koneksi, $id);

    if ($barang != null) {
        $hasil = hapus_barang($koneksi, $id);
        if ($hasil) {
            header('Location: index.php?pesan=hapus_sukses');
            exit;
        } else {
            header('Location: index.php?pesan=gagal');
            exit;
        }
    } else {
        header('Location: index.php?pesan=tidak_ditemukan');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
