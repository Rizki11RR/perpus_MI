# Deployment SIPUS pada shared hosting

## Persiapan

- PHP `8.4.27`.
- MariaDB `11.4.13`.
- Document root diarahkan ke `public`.
- `.env` production dibuat di luar repository bila hosting mendukung.
- `APP_ENV=production`.
- `APP_DEBUG=false`.
- `APP_URL` memakai URL production.
- `APP_KEY` dibuat unik dan tidak dibagikan.
- Database memakai credential production, bukan `.env.example`.

## Deploy

1. Upload source tanpa `.env` lokal, `database/database.sqlite`, backup, dan file rahasia.
2. Jalankan `composer install --no-dev --optimize-autoloader`.
3. Jalankan `npm ci && npm run build` sebelum upload asset, atau upload hasil `public/build`.
4. Jalankan `php artisan migrate --force`.
5. Jalankan `php artisan optimize`.
6. Pastikan `storage` dapat ditulis.
7. Pastikan storage backup tetap privat.
8. Uji login, master data, sirkulasi, laporan, import, dan backup.

## Keamanan

- Jangan expose root project sebagai document root.
- Jangan expose `.env`, `storage/app`, dan backup.
- Jangan aktifkan `APP_DEBUG`.
- Hapus file dump, log debug, dan credential dari public.

## Rollback

1. Aktifkan maintenance bila tersedia.
2. Simpan log error.
3. Kembalikan release source sebelumnya.
4. Restore database hanya dari backup terverifikasi pada staging/prosedur yang disetujui.
5. Jalankan smoke test.
