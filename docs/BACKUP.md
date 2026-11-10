# Prosedur backup dan restore SIPUS

## Backup

1. Login sebagai Admin.
2. Buka `/backup`.
3. Klik `Buat backup terverifikasi`.
4. Pastikan status `verified` dan ukuran file tercatat.
5. Unduh file melalui tombol `Unduh`.

File disimpan pada disk Laravel `local`, bukan `public`.

## Verifikasi

Aplikasi memeriksa file ada dan ukuran file sama dengan `backup_logs.file_size` sebelum download.

## Restore staging

1. Jangan restore pada production.
2. Siapkan database staging kosong.
3. Baca JSON backup dan cocokkan daftar tabel.
4. Restore memakai prosedur database terkontrol.
5. Jalankan `php artisan migrate:fresh --seed --force` hanya pada staging.
6. Jalankan `php artisan test` dan cek login, katalog, sirkulasi, laporan.

Restore fisik belum dijalankan karena membutuhkan environment staging terpisah.
