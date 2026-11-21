<!--
Tujuan: mencatat kontrol keamanan aplikasi SIPUS.
Caller: maintainer, reviewer keamanan, dan operator deployment.
Dependensi: middleware Laravel, validasi input, storage, dan database.
Public utama: kontrol autentikasi, otorisasi, transaksi, dan backup.
Side effect: hanya dokumentasi; tidak melakukan perubahan runtime.
-->
# Audit keamanan Phase 9

## Selesai

- Route operasional memakai `auth` dan `active`.
- Pengaturan, backup, dan download backup memakai `role:admin`.
- Backup memakai storage privat dan verifikasi ukuran.
- Backup tidak menyimpan password atau token pengguna.
- Sirkulasi memakai transaction dan row lock.
- Tabel besar memakai pagination pada UI.
- Query relasi utama memakai eager loading.

## Pemeriksaan eksternal

- Browser desktop/tablet belum tersedia pada sesi ini.
- Shared-hosting staging belum tersedia.
- Restore backup nyata belum dijalankan pada staging.
- Printer fisik belum tersedia.

## Deployment wajib

- `APP_DEBUG=false`.
- Document root menunjuk `public`.
- `.env` dan `storage/app/private` tidak publik.
- Jalankan smoke test setelah deploy.
