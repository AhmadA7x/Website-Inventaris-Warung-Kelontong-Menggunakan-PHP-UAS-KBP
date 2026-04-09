-- Membuat database
CREATE DATABASE IF NOT EXISTS inventaris_warung
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

-- Menggunakan database
USE inventaris_warung;

-- Membuat tabel untuk menyimpan data inventaris barang warung kelontong
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    kategori ENUM('Minuman', 'Makanan Ringan', 'Sembako', 'Rokok & Tembakau', 'Kebersihan & Perawatan', 'Lainnya') NOT NULL DEFAULT 'Lainnya',
    jumlah INT NOT NULL DEFAULT 0,
    stok_minimum INT NOT NULL DEFAULT 5,
    tanggal_masuk DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
