<!--
Tujuan: panduan operasi harian Admin SIPUS.
Caller: admin, petugas, dan maintainer operasional.
Dependensi: route aplikasi, database, dan storage backup.
Public utama: login, master data, sirkulasi, backup, laporan, insiden.
Side effect: memandu perubahan data dan pengelolaan file backup secara manual.
-->
# Panduan operasional Admin SIPUS

## Login

Gunakan username dan password akun aktif. Jangan berbagi password.

## Operasi harian

1. Periksa dashboard.
2. Catat peminjaman pada `/sirkulasi`.
3. Pilih tepat satu siswa atau guru.
4. Pilih satu atau lebih eksemplar tersedia.
5. Catat pengembalian per item.
6. Periksa transaksi aktif dan keterlambatan pada `/laporan`.

## Master data

Kelola kategori, buku, eksemplar, kelas, siswa, guru, dan petugas melalui menu masing-masing. Nonaktifkan data yang sudah direferensikan. Jangan hapus histori transaksi.

## Backup

Admin membuka `/backup`, membuat backup, memastikan status `verified`, lalu mengunduh file bila diperlukan. Simpan salinan pada lokasi privat yang aman.

## Laporan

Gunakan `/laporan` untuk filter dan print. Print barcode tersedia dari daftar eksemplar.

## Insiden

Jika transaksi gagal, jangan ulangi berkali-kali sebelum memeriksa status. Simpan waktu, username, URL, pesan error, dan kode transaksi. Jangan membagikan password atau file `.env`.
