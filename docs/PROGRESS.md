# PROGRESS --- Catatan Pengerjaan SIPUS

Dokumen ini harus diperbarui oleh AI agent setiap selesai mengerjakan
satu task atau menemukan blocker penting.

## Status saat dokumen dibuat

-   [x] Dokumen instruksi awal AI agent dibuat.
-   [x] Kebutuhan produk dan aturan bisnis dasar didokumentasikan.
-   [x] Arsitektur awal dan rancangan database didokumentasikan.
-   [x] Arah desain Mazer dan palet hijau Islami ditentukan.
-   [ ] Repository aplikasi diperiksa.
-   [ ] Laravel project disiapkan.
-   [ ] Integrasi Mazer dimulai.

**Status keseluruhan:** Belum mulai implementasi aplikasi. Dokumen ini
adalah baseline perencanaan.

## Keputusan awal

  Area                   Keputusan
  ---------------------- ---------------------------------------
  Jenis aplikasi         Sistem informasi perpustakaan sekolah
  Backend                Laravel
  Interaksi              Livewire
  Database               MySQL/MariaDB
  UI                     Mazer + Bootstrap 5
  Warna utama            Hijau Islami `#166534`
  Zona waktu             Asia/Jakarta
  Target hosting         Shared hosting/cPanel
  Role                   Admin dan Petugas
  Identitas peminjam     Siswa dan guru terpisah
  Eksemplar              Barcode unik per fisik buku
  Pengembalian           Mendukung parsial
  Denda                  Opsional, default Rp0
  Pembatalan transaksi   Admin saja
  Import                 Preview dan validasi sebelum commit

## Log sesi

Tambahkan entri terbaru di bagian paling atas.

### 2026-10-26 --- Phase 4 selesai, filter dan deaktivasi

-   **Status:** Selesai.
-   **Ringkasan:** Filter katalog memiliki test Livewire. Master buku, kategori, siswa, guru, dan petugas dapat dinonaktifkan tanpa menghapus histori. Data nonaktif tersembunyi dari daftar aktif.
-   **QA:** `php artisan migrate:fresh --seed --force`, `php artisan test` 23 tests passed, 48 assertions, `npm run build` lulus.

### 2026-10-26 --- QA dashboard dan statistik real

-   **Status:** Selesai
-   **Ringkasan:**
    - Dashboard memakai query database real untuk judul, eksemplar, stok, anggota, petugas, dan transaksi terbaru.
    - Empty state transaksi tetap jujur saat database kosong.
    - Statistik tidak memakai angka fiktif.
    - Test dashboard berisi data dan kosong ditambahkan.
-   **File dibuat/diubah:**
    - `app/Livewire/Dashboard.php`
    - `resources/views/livewire/dashboard.blade.php`
    - `tests/Feature/DashboardTest.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 20 tests passed, 40 assertions.
    - `npm run build`: lulus.
    - `php artisan route:list`: lulus.
-   **Blocker/risiko:**
    - Browser click-through tetap perlu dilakukan pada browser lokal.
    - Modal Bootstrap belum menggantikan form inline.
-   **Task berikutnya:**
    - Mulai Phase 5 sirkulasi, tetapi jangan membuat pinjaman sebelum test concurrency dan partial return tersedia.

### 2026-10-26 --- QA CRUD, modal form, alert, error handling

-   **Status:** Selesai
-   **Ringkasan:**
    - SweetAlert2 dipasang untuk konfirmasi hapus kategori, buku, dan eksemplar.
    - Form CRUD tetap memakai inline validation Livewire.
    - Error request Livewire menampilkan dialog error dan mencegah kegagalan diam-diam.
    - Tombol hapus tidak mengeksekusi aksi sebelum konfirmasi.
-   **File dibuat/diubah:**
    - `resources/js/app.js`
    - `resources/views/livewire/books/category-index.blade.php`
    - `resources/views/livewire/books/book-index.blade.php`
    - `resources/views/livewire/books/copy-index.blade.php`
    - `package.json`, `package-lock.json`
    - `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 18 tests passed, 35 assertions.
    - `npm run build`: lulus.
-   **Blocker/risiko:**
    - Form saat ini tampil inline, belum memakai modal Bootstrap.
    - Browser click-through belum dapat dijalankan dari environment ini.
-   **Task berikutnya:**
    - Tambahkan test CRUD anggota dan dashboard statistik real.

### 2026-10-26 --- Phase 4 CRUD kelas, siswa, guru, petugas

-   **Status:** Selesai
-   **Ringkasan:**
    - CRUD kelas dan tahun ajaran dibuat.
    - CRUD siswa dengan NIS unik, kelas, status, pencarian, pagination.
    - CRUD guru dengan NIP opsional unik, jabatan, status, pencarian, pagination.
    - CRUD petugas dengan nomor identitas unik, jabatan, kontak, status aktif.
    - Semua halaman memakai validasi Livewire dan empty state.
-   **File dibuat/diubah:**
    - `app/Livewire/Members/ClassIndex.php`
    - `app/Livewire/Members/StudentIndex.php`
    - `app/Livewire/Members/TeacherIndex.php`
    - `app/Livewire/Staff/StaffIndex.php`
    - `resources/views/livewire/members/*.blade.php`
    - `resources/views/livewire/staff/staff-index.blade.php`
    - `routes/web.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 18 tests passed, 35 assertions.
    - `npm run build`: lulus.
-   **Blocker/risiko:**
    - Test khusus CRUD anggota belum ditambahkan.
    - Dashboard statistik belum memakai query domain.
-   **Task berikutnya:**
    - Tambahkan test CRUD anggota dan dashboard statistik real.

### 2026-10-26 --- Phase 4 barcode Code 128 dan cetak label

-   **Status:** Selesai
-   **Ringkasan:**
    - `picqer/php-barcode-generator` `3.3.0` dipasang.
    - `BarcodeService` menghasilkan PNG Code 128.
    - Endpoint barcode dilindungi autentikasi dan status aktif.
    - Halaman label barcode tersedia dengan CSS print sederhana.
-   **File dibuat/diubah:**
    - `app/Services/BarcodeService.php`
    - `app/Http/Controllers/BarcodeController.php`
    - `resources/views/print/barcode-labels.blade.php`
    - `routes/web.php`
    - `tests/Feature/BarcodeTest.php`
    - `composer.json`, `composer.lock`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 18 tests passed, 35 assertions.
    - `npm run build`: lulus.
    - PNG diverifikasi memiliki signature PNG.
-   **Blocker/risiko:**
    - Cetak massal belum tersedia; MVP saat ini mencetak satu label per URL.
    - Konfirmasi hapus SweetAlert2 belum diterapkan.
-   **Task berikutnya:**
    - CRUD kelas, siswa, guru, dan petugas.

### 2026-10-26 --- Phase 4 CRUD eksemplar dan QA dasar

-   **Status:** Selesai
-   **Ringkasan:**
    - CRUD eksemplar dibuat dengan Livewire.
    - Barcode divalidasi unik di server.
    - Search barcode/judul, filter buku, pagination, dan empty state tersedia.
    - Eksemplar yang memiliki histori transaksi tidak dapat dihapus.
    - Status `borrowed` tidak dapat ditetapkan sembarang melalui form edit.
-   **File dibuat/diubah:**
    - `app/Livewire/Books/CopyIndex.php`
    - `resources/views/livewire/books/copy-index.blade.php`
    - `routes/web.php`
    - `tests/Feature/CopyCrudTest.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 16 tests passed, 31 assertions.
    - `npm run build`: lulus.
-   **Blocker/risiko:**
    - Code 128 dan cetak label belum dibuat.
    - Konfirmasi hapus SweetAlert2 belum diterapkan.
-   **Task berikutnya:**
    - Implementasikan generator barcode Code 128 dan halaman cetak label.

### 2026-10-26 --- Phase 4 CRUD katalog buku

-   **Status:** Selesai
-   **Ringkasan:**
    - CRUD judul buku dibuat dengan Livewire.
    - Form mencakup kategori, kode, ISBN, judul, penulis, penerbit, tahun, deskripsi.
    - Search server-side berdasarkan judul, kode buku, dan ISBN.
    - Filter kategori dan pagination tersedia.
    - Buku yang sudah memiliki eksemplar tidak dapat dihapus.
-   **File dibuat/diubah:**
    - `app/Livewire/Books/BookIndex.php`
    - `resources/views/livewire/books/book-index.blade.php`
    - `routes/web.php`
    - `tests/Feature/BookCrudTest.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 14 tests passed, 28 assertions.
    - `npm run build`: lulus.
    - Parse error pada query awal diperbaiki lalu test diulang sampai lulus.
-   **Blocker/risiko:**
    - Konfirmasi hapus masih memakai action langsung; SweetAlert2 perlu ditambahkan sebelum finalisasi UI.
-   **Task berikutnya:**
    - CRUD eksemplar dan validasi barcode.

### 2026-10-26 --- Phase 4 awal: logo dan kategori buku

-   **Status:** Sebagian
-   **Ringkasan:**
    - `docs/logo.jpeg` dipasang sebagai `public/images/logo.jpeg`.
    - Profil menampilkan logo dengan alt text dan tidak membuka upload file tanpa validasi keamanan.
    - CRUD kategori buku dibuat dengan Livewire.
    - CRUD kategori memiliki pencarian, pagination, validasi nama unik, empty state, edit, dan proteksi hapus jika sudah dipakai buku.
-   **File dibuat/diubah:**
    - `public/images/logo.jpeg`
    - `app/Livewire/Settings/LibraryProfile.php`
    - `resources/views/livewire/settings/library-profile.blade.php`
    - `app/Livewire/Books/CategoryIndex.php`
    - `resources/views/livewire/books/category-index.blade.php`
    - `routes/web.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 12 tests passed, 25 assertions.
    - `npm run build`: lulus.
-   **Blocker/risiko:**
    - CRUD buku dan eksemplar belum dibuat.
    - Tombol hapus kategori belum memakai SweetAlert2; perlu ditambahkan sebelum task UI dianggap final.
-   **Task berikutnya:**
    - CRUD judul buku, lalu eksemplar.

### 2026-10-26 --- Phase 3 profil, pengaturan, audit

-   **Status:** Selesai
-   **Ringkasan:**
    - Halaman Livewire profil perpustakaan dibuat.
    - Halaman Livewire pengaturan sirkulasi dibuat.
    - Validasi server diterapkan untuk limit pinjaman, durasi, denda, masa tenggang, dan timezone.
    - Pengaturan hanya dapat diakses Admin melalui middleware backend.
    - Perubahan pengaturan dicatat di `activity_logs`.
-   **File dibuat/diubah:**
    - `app/Livewire/Settings/LibraryProfile.php`
    - `app/Livewire/Settings/LibrarySettings.php`
    - `resources/views/livewire/settings/library-profile.blade.php`
    - `resources/views/livewire/settings/library-settings.blade.php`
    - `routes/web.php`
    - `tests/Feature/SettingsTest.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 12 tests passed, 25 assertions.
    - `php artisan route:list`: lulus.
-   **Blocker/risiko:**
    - CRUD profil belum memiliki upload logo; upload akan dibuat setelah aturan keamanan file ditetapkan.
-   **Task berikutnya:**
    - Mulai Phase 4 master data, dimulai dari kategori dan katalog buku.

### 2026-10-26 --- Phase 2 test factory sirkulasi

-   **Status:** Selesai
-   **Ringkasan:**
    - Factory `Loan` dan `LoanItem` dibuat.
    - Test pembuatan transaksi siswa dan item pinjaman dibuat.
    - Test memastikan relationship loan-item berjalan.
-   **File dibuat/diubah:**
    - `database/factories/LoanFactory.php`
    - `database/factories/LoanItemFactory.php`
    - `tests/Feature/DomainFactoryTest.php`
    - `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 10 tests passed, 22 assertions.
    - `php artisan migrate:fresh --seed --force`: lulus.
-   **Blocker/risiko:**
    - CHECK tepat satu peminjam masih belum diterapkan.
-   **Task berikutnya:**
    - Mulai implementasi Phase 3 profil, pengaturan, dan audit.

### 2026-10-26 --- Phase 2 factory dan seeder lokal

-   **Status:** Selesai
-   **Ringkasan:**
    - Factory master data dibuat untuk staff, kelas, siswa, guru, kategori, buku, dan eksemplar.
    - Model domain memakai `HasFactory`.
    - Seeder lokal membuat admin hanya jika `SIPUS_DEV_ADMIN_PASSWORD` diisi.
    - Seeder lokal membuat satu kategori, judul, dan eksemplar contoh; tidak berjalan pada testing dan tidak berjalan production.
-   **File dibuat/diubah:**
    - `database/factories/*Factory.php`
    - `app/Models/*.php`
    - `database/seeders/DatabaseSeeder.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `composer dump-autoload`: lulus.
    - `php artisan test`: 9 tests passed, 19 assertions.
-   **Blocker/risiko:**
    - Factory sirkulasi dan support belum diperlukan sebelum service bisnis dibuat.
-   **Task berikutnya:**
    - Tambahkan test factory/seeder lalu lanjutkan sisa integritas database Phase 2.

### 2026-10-26 --- Phase 2 model dan relationship

-   **Status:** Sebagian
-   **Ringkasan:**
    - Model Eloquent domain dibuat untuk seluruh tabel SIPUS.
    - Relationship utama dibuat untuk user, staff, kelas, anggota, buku, eksemplar, transaksi, import, audit, dan backup.
    - Cast tanggal, decimal, JSON, boolean, dan status dasar diterapkan.
    - Test uniqueness barcode dan NIS serta relasi eksemplar ke buku ditambahkan.
-   **File dibuat/diubah:**
    - `app/Models/*.php` untuk model domain.
    - `tests/Feature/DomainIntegrityTest.php`
    - `database/factories/UserFactory.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 10 tests passed, 20 assertions.
    - `php artisan migrate:fresh --seed --force`: lulus.
-   **Keputusan baru:**
    - Model memakai `$guarded = []` sementara karena validasi trust boundary akan berada di Form Request/Livewire sebelum operasi CRUD.
-   **Blocker/risiko:**
    - Factory domain dan seeder data contoh belum dibuat.
    - CHECK tepat satu peminjam belum tersedia.
-   **Task berikutnya:**
    - Buat factory domain minimal dan seeder contoh lokal tanpa data fiktif yang tampak sebagai data produksi.

### 2026-10-26 --- Phase 2 awal: migration domain SIPUS

-   **Status:** Sebagian
-   **Ringkasan:**
    - Migration master data dibuat: staff, classes, students, teachers, book_categories, books, book_copies.
    - Migration sirkulasi dibuat: loans dan loan_items.
    - Migration support dibuat: library_profiles, library_settings, activity_logs, import_batches, import_batch_rows, backup_logs.
    - Foreign key, unique constraint, index, status default, dan decimal denda diterapkan.
-   **File dibuat/diubah:**
    - `database/migrations/2026_10_26_070659_create_library_master_tables.php`
    - `database/migrations/2026_10_26_070715_create_library_circulation_tables.php`
    - `database/migrations/2026_10_26_070729_create_library_support_tables.php`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan migrate:fresh --seed --force`: lulus.
    - `php artisan test`: 7 tests passed, 16 assertions.
-   **Keputusan baru:**
    - Skema dibagi menjadi tiga migration domain agar rollback dan review lebih mudah.
    - Constraint tepat satu peminjam pada `loans` belum ditambahkan karena kompatibilitas MariaDB dan SQLite test perlu diuji terpisah.
-   **Blocker/risiko:**
    - Model Eloquent dan relationship belum dibuat.
    - Factory, seeder contoh, dan test integritas belum dibuat.
-   **Task berikutnya:**
    - Buat model Eloquent, relationship, factory, dan test constraint Phase 2.

### 2026-10-26 --- Phase 1 selesai: role, akun aktif, otorisasi

-   **Status:** Selesai
-   **Ringkasan:**
    - Role `admin` dan `petugas` ditambahkan ke users.
    - Status akun `is_active` ditambahkan.
    - Middleware `active` menolak akun nonaktif.
    - Middleware `role` melindungi route berdasarkan role di backend.
    - Seeder development hanya membuat admin jika `SIPUS_DEV_ADMIN_PASSWORD` tersedia pada environment local/testing.
    - Login username menolak akun nonaktif.
-   **File dibuat/diubah:**
    - `database/migrations/2026_10_26_071000_add_role_and_active_to_users_table.php`
    - `app/Models/User.php`
    - `app/Http/Middleware/EnsureUserIsActive.php`
    - `app/Http/Middleware/EnsureUserHasRole.php`
    - `bootstrap/app.php`
    - `app/Http/Controllers/AuthController.php`
    - `database/seeders/DatabaseSeeder.php`
    - `database/factories/UserFactory.php`
    - `routes/web.php`
    - `tests/Feature/AuthRoleTest.php`
    - `.env.example`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan migrate:fresh --seed --force`: lulus.
    - `php artisan test`: 7 tests passed, 16 assertions.
    - `npm run build`: lulus.
    - `php artisan pint --test`: tidak tersedia sebagai command Artisan pada skeleton ini; bukan error aplikasi.
-   **Blocker/risiko:**
    - `users.email` masih wajib dari skeleton Laravel; akan dirapikan pada task foundation berikut jika strategi username final tetap dipakai.
-   **Task berikutnya:**
    - Audit auth dengan browser/HTTP, lalu mulai migration domain Phase 2.

### 2026-10-26 --- Phase 1 awal: Livewire, layout Mazer, login

-   **Status:** Sebagian
-   **Ringkasan:**
    - Livewire terpasang dan dashboard page component aktif.
    - Layout Blade bergaya Mazer dibuat dengan sidebar, topbar, logout, dan focus state.
    - Login/logout berbasis username dibuat dengan session regeneration.
    - Palet hijau dari `DESIGN.md` diterapkan melalui CSS custom properties.
    - Dashboard memakai empty state jujur, tanpa angka fiktif.
-   **File dibuat/diubah:**
    - `resources/css/app.css`
    - `resources/js/app.js`
    - `resources/views/layouts/app.blade.php`
    - `resources/views/components/layouts/app.blade.php`
    - `resources/views/auth/login.blade.php`
    - `resources/views/livewire/dashboard.blade.php`
    - `app/Http/Controllers/AuthController.php`
    - `app/Livewire/Dashboard.php`
    - `routes/web.php`
    - `database/migrations/*_add_username_to_users_table.php`
    - `database/factories/UserFactory.php`
    - `tests/Feature/ExampleTest.php`
    - `package.json`, `package-lock.json`
    - `docs/TASK.md`, `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - `php artisan test`: 3 tests passed, 4 assertions.
    - `npm run build`: berhasil.
-   **Keputusan baru:**
    - Login form memakai field `username`; model legacy Laravel tetap mempertahankan `email` sampai foundation auth selesai.
-   **Blocker/risiko:**
    - Role Admin/Petugas belum dibuat.
    - Seeder akun development aman belum dibuat.
    - Mazer source belum dipindahkan penuh; saat ini shell memakai Bootstrap dan Bootstrap Icons.
-   **Task berikutnya:**
    - Tambahkan role, middleware/policy, seeder tanpa password tetap, dan test akses role.

### 2026-10-26 --- Phase 0 selesai: Laravel, Livewire, Docker MariaDB

-   **Status:** Selesai
-   **Ringkasan:**
    - Project Laravel berjalan dengan database MariaDB Docker.
    - Migrations default berhasil.
    - Database memakai MariaDB `11.4.13`, sama dengan hosting.
-   **File dibuat/diubah:**
    - `compose.yaml`
    - `.env`, `.env.example`
    - `composer.json`, `composer.lock`
    - `docs/TASK.md`
    - `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - **Perintah:** `docker compose up -d mariadb`
    - **Hasil:** container `web_perpus-mariadb-1` aktif.
    - **Perintah:** `docker compose exec -T mariadb mariadb-admin ping -usipus -psipus_local`
    - **Hasil:** `mysqld is alive`.
    - **Perintah:** `php artisan migrate`
    - **Hasil:** seluruh migration default Laravel lulus.
-   **Keputusan baru:**
    - Phase 0 memakai Docker Compose MariaDB dengan port host `3307`.
-   **Blocker/risiko:**
    - Smoke test HTTP dan asset build belum dijalankan.
    - Mazer belum diintegrasikan.
-   **Task berikutnya:**
    - Mulai Phase 1: integrasi Livewire, layout Mazer, autentikasi, dan role.

### 2026-10-26 --- Setup Laravel 12, Livewire, dan Docker MariaDB

-   **Status:** Sebagian
-   **Ringkasan:**
    - Konfigurasi hosting dikonfirmasi: PHP `8.4.27`, MariaDB `11.4.13`.
    - Laravel `12.70.1` dan Livewire `3.8.11` dipasang.
    - Docker Compose MariaDB `11.4` dibuat untuk database lokal.
    - Login ditetapkan memakai username.
-   **File dibuat/diubah:**
    - `composer.json`, `composer.lock`
    - `.env.example`
    - `compose.yaml`
    - `docs/TASK.md`
    - `docs/ARCHITECTURE.md`
    - `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - **Perintah:** `composer create-project laravel/laravel:^12.0 /tmp/opencode/sipus-laravel`
    - **Hasil:** berhasil; Laravel `12.70.1` terpasang.
    - **Perintah:** `composer require livewire/livewire:^3.0`
    - **Hasil:** berhasil; Livewire `3.8.11` terpasang.
    - **Perintah:** `docker --version; docker compose version`
    - **Hasil:** Docker `29.6.2`, Compose `v5.3.1`.
-   **Keputusan baru:**
    - Docker lokal menggunakan MariaDB `11.4`, port host `3307`.
-   **Blocker/risiko:**
    - Container/database belum dijalankan.
    - Mazer belum dipindahkan ke asset pipeline Laravel.
-   **Task berikutnya:**
    - Jalankan container MariaDB, konfigurasi `.env`, migrasi, dan smoke test HTTP.

### 2026-10-26 --- Discovery repository dan environment

-   **Status:** Sebagian
-   **Ringkasan:**
    - Repository `web_perpus` belum memiliki project Laravel; hanya dokumen proyek.
    - PHP 8.3.6, Composer 2.10.2, Node 22.23.3, npm 10.9.9 tersedia.
    - Extension `pdo_mysql`, `mbstring`, `xml`, `fileinfo`, `zip`, dan `openssl` tersedia; binary MySQL tersedia.
    - Referensi Mazer tersedia di `../referensi_dashboard`, versi package Mazer 2.3.1 dan Bootstrap 5.3.3.
-   **File dibuat/diubah:**
    - `docs/TASK.md`
    - `docs/PROGRESS.md`
-   **Pemeriksaan/test:**
    - **Perintah:** `php -v; composer --version; node --version; npm --version`
    - **Hasil:** versi lokal terdeteksi seperti di atas.
    - **Perintah:** pemeriksaan struktur `web_perpus` dan `referensi_dashboard`.
    - **Hasil:** dependency Laravel/Livewire belum tersedia; `referensi_dashboard/node_modules` belum terpasang.
-   **Keputusan baru:**
    - Belum ada keputusan versi Laravel/Livewire karena batas PHP shared hosting belum dikonfirmasi.
-   **Blocker/risiko:**
    - Wajib konfirmasi versi PHP shared hosting sebelum membuat project dan memasang dependency.
    - Kredensial database lokal, strategi login username/email, dan kapabilitas hosting belum tersedia.
-   **Task berikutnya:**
    - Konfirmasi detail hosting dan database lokal, lalu siapkan project Laravel pada Phase 0.

### YYYY-MM-DD --- Judul pekerjaan

-   **Status:** Selesai / Sebagian / Terblokir
-   **Ringkasan:**
    -   ...
-   **File dibuat/diubah:**
    -   ...
-   **Pemeriksaan/test:**
    -   Perintah:
    -   Hasil:
-   **Keputusan baru:**
    -   ...
-   **Blocker/risiko:**
    -   ...
-   **Task berikutnya:**
    -   ...

## Template ringkasan akhir sesi AI agent

Setiap sesi coding harus menutup dengan: 1. Tujuan task. 2. File yang
dibuat/diubah. 3. Perilaku yang sudah berfungsi. 4. Test/build yang
dijalankan dan hasilnya. 5. Task yang ditandai selesai di `TASK.md`. 6.
Risiko atau blocker yang belum terselesaikan. 7. Satu rekomendasi task
berikutnya.

## Catatan keputusan yang belum final

-   Versi PHP/Laravel/Livewire yang tersedia di shared hosting.
-   Login memakai username atau email.
-   Apakah guru memiliki aturan pinjam berbeda.
-   Format kode transaksi.
-   Aturan final denda dan hari kalender/operasional.
-   Perlakuan pengembalian buku rusak/hilang.
-   Kebijakan commit import campuran valid/invalid.
-   Kapabilitas SSH, cron, dan backup pada paket hosting.

### 2026-10-26 --- Phase 5 QA concurrency dan loading state

- Test peminjaman ulang eksemplar yang sama ditambahkan.
- `LoanService` mengunci borrower dan eksemplar memakai `lockForUpdate()`.
- Loading state Livewire ditambahkan pada form sirkulasi.
- Batas pinjaman aktif per peminjam diuji.
- QA: 32 tests passed, 64 assertions; build dan route list lulus.

### 2026-10-26 --- Phase 5 fondasi sirkulasi

- `LoanService` mencatat pinjaman dalam transaksi DB dan memakai `lockForUpdate()` pada eksemplar.
- Validasi tepat satu peminjam, batas jumlah buku, ketersediaan, jatuh tempo, kode transaksi, dan snapshot denda tersedia.
- Pengembalian parsial dan penuh memperbarui status item, eksemplar, denda, dan transaksi.
- Halaman `/sirkulasi` tersedia untuk pinjam dan kembali.
- QA: `php artisan test` 26 tests passed, 54 assertions; `npm run build` dan `php artisan route:list` lulus.
- Blocker: searchable multi-book picker, pembatalan admin, dan concurrency test khusus belum selesai.

### 2026-10-26 --- Phase 5 acceptance checklist selesai

- Seluruh task sirkulasi ditandai selesai berdasarkan implementasi dan test yang tersedia.
- Peminjaman, pengembalian parsial/penuh, denda, pembatalan admin, row lock, filter, picker, dan loading state tersedia.
- QA terakhir: 32 tests passed, 64 assertions; `npm run build` lulus.
- Catatan: browser click-through lintas breakpoint masih pemeriksaan manual deployment.

### 2026-10-26 --- Phase 6 laporan, export, dan print

- Laporan transaksi, katalog, siswa, dan guru tersedia dengan filter, pagination, empty state, dan print.
- Export CSV dan JSON tersedia untuk katalog dan transaksi.
- Print katalog, print transaksi, dan print barcode massal tersedia.
- QA: `php artisan test` 36 tests passed, 77 assertions; `npm run build` lulus.
- Blocker: uji hasil print dengan printer fisik belum dapat dilakukan dari environment ini.

### 2026-10-27 --- Phase 7 import CSV selesai

- Template CSV buku, siswa, dan guru tersedia.
- Upload dibatasi CSV/TXT dan 2 MB.
- Preview tersimpan ke staging batch.
- Validasi header, kolom wajib, kategori, kelas, duplikasi database, dan duplikasi antarbaris.
- Commit baris valid memakai transaction.
- Batch hanya dapat di-commit pemiliknya.
- Preview dapat dibatalkan.
- Activity log import tercatat.
- QA: `php artisan test` 40 tests passed, 84 assertions; build dan route import lulus.

### 2026-10-27 --- Phase 8 backup selesai, Phase 9 hardening awal

- Backup privat, log, riwayat Admin, download terotorisasi, verifikasi ukuran, dan dokumentasi restore tersedia.
- Deployment guide `docs/DEPLOYMENT.md` dibuat.
- Cache production diverifikasi: config, route, dan view.
- QA: 43 tests passed, 89 assertions; build lulus.
- Blocker: restore staging nyata dan click-through browser/printer membutuhkan environment eksternal.

### 2026-10-27 --- Phase 9 hardening awal

- Audit route auth/active dan role Admin untuk admin, backup, dan operational routes.
- Audit route parameter export memakai `whereIn`.
- Audit build cache production.
- Bug production cache terdeteksi dan diperbaiki: nilai kosong `publication_year` disimpan sebagai `null`, bukan string kosong.
- QA: 45 tests passed, 103 assertions; build, route list, config cache, route cache, dan view cache lulus.
- Blocker: browser desktop/tablet, shared-hosting staging, restore nyata, dan log production masih membutuhkan environment eksternal.

### 2026-10-27 --- Phase 9 audit kode selesai

- Smoke test checklist `docs/SMOKE-TEST.md` dibuat.
- Audit authorization, validation/upload, N+1/pagination, concurrency, test suite, build, dan cache production selesai.
- Backup user payload tidak memuat password.
- Seluruh test: 45 passed, 103 assertions.
- Browser, shared-hosting staging, restore staging, dan printer tetap BLOCKED karena environment eksternal tidak tersedia.

### 2026-10-27 --- Phase 9 pemeriksaan error dan platform

- `php -l` pada service/component utama lulus.
- `composer check-platform-reqs` lulus.
- Log lama berisi error historis dari koneksi DB, cache Livewire, dan percobaan pengembangan; tidak ada error syntax aktif pada pemeriksaan sekarang.
- Error log lama tidak dihapus agar jejak audit tetap ada.
- Browser, shared-hosting staging, dan restore staging tetap BLOCKED oleh keterbatasan environment.

### 2026-10-27 --- Dokumentasi operasional selesai

- Panduan Admin `docs/OPERATIONS.md` dibuat.
- Checklist deployment, operasi harian, import, backup, laporan, dan insiden tersedia.
- Task panduan instalasi/deployment/operasional ditandai selesai.
- External release checks tetap BLOCKED: browser, staging shared-hosting, `APP_DEBUG=false` production, storage privat hosting, restore, dan printer.

### 2026-10-27 --- Akun petugas dan import/export massal

- Menu Data Petugas sekarang membuat dan mengubah akun login terkait.
- Field akun: email, username, password; role otomatis `petugas`.
- Password wajib saat akun baru, opsional saat edit.
- Status petugas disinkronkan ke status akun login.
- Export CSV/JSON tersedia untuk buku, siswa, dan guru; export transaksi tetap tersedia.
- Import CSV/TXT buku, siswa, dan guru tetap melalui preview, validasi, lalu commit.
- QA: 45 tests passed, 103 assertions; build Vite lulus.

### 2026-10-27 --- Scan barcode sirkulasi

- Input scan barcode peminjaman menerima scanner USB mode keyboard dan Enter.
- Barcode valid tersedia otomatis masuk daftar eksemplar terpilih.
- Barcode tidak ditemukan, tidak tersedia, atau duplikat ditolak dengan pesan.
- Input scan pengembalian mencari item pinjaman aktif dan mengembalikan otomatis.
- Tidak ada migration baru.
- QA: 45 tests passed, 103 assertions; syntax check lulus.

### 2026-10-27 --- Kelas disederhanakan

- Form Kelas kini hanya memakai nama kelas.
- Tingkat dan tahun ajaran dihapus dari UI dan schema.
- Nama kelas dibuat unik.
- Migration `2026_10_27_120000_simplify_classes_to_name_only.php` dijalankan lokal.
- QA: 45 tests passed, 103 assertions.
