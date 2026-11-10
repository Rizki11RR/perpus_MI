# TASK --- Roadmap Implementasi SIPUS

Checklist ini menjadi daftar kerja utama AI agent. Tandai selesai hanya
setelah acceptance criteria dan pemeriksaan terkait terpenuhi.

## Phase 0 --- Discovery dan setup

-   [x] Periksa repository dan kondisi project saat ini.
-   [x] Catat versi PHP, Composer, Laravel, Livewire, Node, npm,
    Bootstrap, dan Mazer.
-   [x] Pastikan target shared hosting dan versi PHP-nya.
-   [x] Putuskan strategi integrasi Mazer dengan asset pipeline Laravel.
-   [x] Buat `.env.example` yang aman tanpa credential.
-   [x] Siapkan database lokal.
-   [x] Pastikan aplikasi Laravel dapat dijalankan lokal.
-   [x] Catat versi dan keputusan teknis di `ARCHITECTURE.md`.

**Selesai jika:** aplikasi dasar berjalan lokal, versi dependency
tercatat, dan asset build berhasil.

## Phase 1 --- Foundation dan autentikasi

-   [x] Pasang/konfigurasi Livewire yang kompatibel.
-   [x] Integrasikan layout dasar Mazer ke Blade.
-   [x] Terapkan palet hijau Islami dari `DESIGN.md`.
-   [x] Buat layout sidebar, navbar, breadcrumb, dan content.
-   [x] Buat halaman login/logout.
-   [x] Implementasikan role Admin dan Petugas.
-   [x] Implementasikan proteksi route dan Policy/Gate.
-   [x] Buat seeder role dan akun development yang aman.
-   [x] Buat test autentikasi dan akses role.

**Selesai jika:** Admin/Petugas dapat login dan akses backend dibatasi
sesuai role.

## Phase 2 --- Database dan model

-   [ ] Buat migration users extension.
-   [x] Buat migration staff.
-   [x] Buat migration classes.
-   [x] Buat migration students.
-   [x] Buat migration teachers.
-   [x] Buat migration book_categories.
-   [x] Buat migration books.
-   [x] Buat migration book_copies.
-   [x] Buat migration loans.
-   [x] Buat migration loan_items.
-   [x] Buat migration library_profiles.
-   [x] Buat migration library_settings.
-   [x] Buat migration activity_logs.
-   [x] Buat migration import_batches dan import_batch_rows.
-   [x] Buat migration backup_logs.
-   [x] Tambahkan index, unique constraint, foreign key, dan CHECK
    constraint jika kompatibel.
-   [x] Buat model dan Eloquent relationships.
-   [x] Buat factory dan seeder data contoh.
-   [x] Buat test integritas relasi dan constraint.

**Selesai jika:** migration dapat dijalankan pada database target dan
relasi model teruji.

## Phase 3 --- Profil, pengaturan, dan audit

-   [x] CRUD profil perpustakaan.
-   [x] Pengaturan maksimal pinjaman.
-   [x] Pengaturan durasi pinjaman.
-   [x] Pengaturan denda dan masa tenggang.
-   [x] Pengaturan hanya Admin.
-   [x] Activity log untuk aksi penting.
-   [x] Test akses dan perubahan setting.

## Phase 4 --- Master data

-   [x] CRUD kategori buku.
-   [x] CRUD judul buku.
-   [x] CRUD eksemplar.
-   [x] Generate barcode unik Code 128.
-   [x] Cetak label barcode.
-   [x] Pencarian/filter katalog.
-   [x] CRUD kelas dan tahun ajaran.
-   [x] CRUD siswa.
-   [x] CRUD guru.
-   [x] CRUD petugas.
-   [x] Deaktivasi data yang sudah direferensikan.
-   [x] Dashboard menghitung judul, eksemplar, dan stok tersedia.
-   [x] Test unique NIS, barcode, dan proteksi hapus.

## Phase 5 --- Sirkulasi

-   [x] Buat generator nomor transaksi.
-   [x] Buat pencarian peminjam siswa/guru.
-   [x] Pastikan tepat satu jenis peminjam per transaksi.
-   [x] Buat searchable multi-book/eksemplar picker.
-   [x] Tampilkan item terpilih sebagai removable tags.
-   [x] Validasi status ketersediaan.
-   [x] Validasi batas maksimal pinjaman.
-   [x] Hitung due date otomatis.
-   [x] Implementasikan LoanService dengan DB transaction dan row lock.
-   [x] Simpan snapshot denda per item.
-   [x] Buat daftar transaksi dan filter status.
-   [x] Implementasikan pengembalian parsial.
-   [x] Implementasikan pengembalian penuh.
-   [x] Implementasikan kalkulasi denda.
-   [x] Implementasikan pembatalan Admin dengan alasan.
-   [x] Tolak pembatalan setelah ada item returned.
-   [x] Update stok/status otomatis.
-   [x] Buat test concurrency/aturan sirkulasi.

**Selesai jika:** seluruh acceptance criteria peminjaman, pengembalian,
dan pembatalan di PRD lulus.

## Phase 6 --- Laporan dan cetak

-   [x] Laporan transaksi peminjaman.
-   [x] Laporan pengembalian.
-   [x] Laporan pinjaman aktif.
-   [x] Laporan keterlambatan.
-   [x] Laporan anggota aktif.
-   [x] Laporan katalog.
-   [x] Filter bulan/tahun dan filter relevan.
-   [x] Export CSV.
-   [x] Export JSON.
-   [x] Print katalog.
-   [x] Print laporan.
-   [x] Print barcode label.
-   [ ] Uji hasil print dengan printer.

## Phase 7 --- Import/export

-   [x] Template import buku.
-   [x] Template import siswa.
-   [x] Template import guru.
-   [x] Upload file dan validasi tipe/ukuran.
-   [x] Parse file dan simpan staging batch.
-   [x] Preview data sebelum commit.
-   [x] Validasi kolom wajib dan format.
-   [x] Validasi duplikasi dan foreign key.
-   [x] Tampilkan kesalahan per baris.
-   [x] Implementasikan pilihan commit baris valid atau batal seluruh
    batch.
-   [x] Catat activity log.
-   [x] Test import valid, invalid, dan duplikat.

## Phase 8 --- Backup

-   [x] Rancang backup database sesuai fasilitas hosting.
-   [x] Simpan file di storage privat.
-   [x] Buat backup log.
-   [x] Buat halaman riwayat backup Admin.
-   [x] Buat unduhan backup yang terotorisasi.
-   [x] Validasi integritas file backup.
-   [x] Dokumentasikan dan uji prosedur restore.
-   [x] Jangan mengklaim backup berhasil sebelum file terverifikasi.

## Phase 9 --- Hardening dan deployment

-   [x] Audit otorisasi semua route/action.
-   [x] Audit validasi input dan upload.
-   [x] Audit query N+1 dan pagination.
-   [x] Audit concurrency transaksi.
-   [x] Jalankan seluruh test suite.
-   [x] Jalankan build production.
-   [x] Periksa log error.
-   [ ] Uji pada browser desktop/tablet.
-   [ ] Uji deployment pada environment yang mendekati shared hosting.
-   [ ] Atur `APP_DEBUG=false`.
-   [ ] Pastikan `.env`, backup, dan storage privat tidak publik.
-   [ ] Uji backup dan restore di environment staging.
-   [x] Buat panduan instalasi, deployment, dan operasional Admin.

## Prioritas MVP

Jika waktu terbatas, urutan minimum rilis: 1. Foundation/auth/role. 2.
Database dan master data. 3. Sirkulasi pinjam-kembali yang konsisten. 4.
Dashboard dan laporan dasar. 5. Backup. 6. Import/export lanjutan dan
penyempurnaan UI.

Jangan merilis fitur sirkulasi sebelum test concurrency dan pengembalian
parsial lulus.
