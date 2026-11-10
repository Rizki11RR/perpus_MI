# ARCHITECTURE --- Arsitektur Teknis SIPUS

## 1. Prinsip arsitektur

-   Monolith Laravel agar sederhana untuk shared hosting.
-   Livewire untuk interaksi dinamis tanpa membangun SPA terpisah.
-   MySQL/MariaDB InnoDB sebagai database relasional.
-   Mazer Bootstrap 5 sebagai fondasi UI.
-   Service/action layer untuk proses bisnis penting.
-   Database transaction dan row locking untuk menjaga konsistensi
    sirkulasi.
-   File backup dan file import berada pada storage privat.
-   Tidak membutuhkan Redis, queue worker permanen, websocket, atau
    Docker pada production MVP.

## 2. Stack

Keputusan versi Phase 0:

- PHP lokal dan target shared hosting: `8.4.27`.
- Laravel: `12.70.1` melalui skeleton `laravel/laravel:^12.0`.
- Livewire: `3.8.11`.
- Database lokal: MariaDB `11.4` melalui Docker Compose.
- Database hosting: MariaDB `11.4.13`.
- Bootstrap/Mazer: Bootstrap `5.3.3`, Mazer `2.3.1` dari `referensi_dashboard`.
- Login: username.
- Asset pipeline: Vite Laravel; source Mazer dipindahkan selektif ke asset Laravel, tanpa CDN dan tanpa jQuery.
- Production shared hosting: Node tidak diperlukan; upload hasil `npm run build`.

  Area                Pilihan
  ------------------- --------------------------------------------------------
  Backend             Laravel
  UI dinamis          Livewire
  Database            MySQL/MariaDB
  UI framework        Bootstrap 5
  Admin template      Mazer
  Build               Vite / toolchain yang kompatibel dengan versi template
  Notifikasi          SweetAlert2 (konfirmasi), toast
  Barcode             Generator Code 128 yang kompatibel dengan PHP
  Export              CSV/JSON; library tambahan hanya jika dibutuhkan
  Hosting             Shared hosting/cPanel
  Local development   Linux Mint

Versi pasti dependency ditentukan setelah memeriksa PHP pada hosting
target dan versi Laravel/Livewire yang kompatibel. Jangan menambahkan
dependency hanya karena populer.

## 3. Integrasi Mazer

Repository sumber: https://github.com/zuramai/mazer

Mazer adalah template admin berbasis Bootstrap 5. Gunakan source
template, bukan menyalin semua file demo secara membabi buta.

Langkah integrasi: 1. Clone/download Mazer dan periksa `README`,
`package.json`, `vite.config.js`, serta struktur `src`. 2. Identifikasi
versi Bootstrap, cara build, komponen, icon, dan struktur SCSS aktual.
3. Ambil layout, sidebar, navbar, card, table, modal, form, badge, dan
komponen yang dibutuhkan. 4. Integrasikan ke layout Blade Laravel. 5.
Satukan asset melalui satu pipeline build yang konsisten. 6. Jangan
memuat Bootstrap dua kali. 7. Jangan mencampur asset CDN dan asset lokal
untuk library yang sama. 8. Jangan mengedit hasil compiled CSS secara
langsung jika tersedia source SCSS. 9. Simpan perubahan warna pada file
override/theme khusus agar mudah diperbarui. 10. Hapus halaman demo dan
plugin yang tidak digunakan.

Mazer tidak menentukan arsitektur backend. Semua operasi data tetap
melalui Laravel/Livewire.

## 4. Struktur domain

### 4.1 Master data

-   `users`: akun login.
-   `staff`: profil petugas, dapat terhubung ke akun.
-   `classes`: kelas/tahun ajaran.
-   `students`: identitas siswa.
-   `teachers`: identitas guru.
-   `book_categories`: kategori.
-   `books`: judul/bibliografi.
-   `book_copies`: eksemplar fisik.

### 4.2 Sirkulasi

-   `loans`: header transaksi.
-   `loan_items`: detail eksemplar yang dipinjam.

### 4.3 Konfigurasi dan audit

-   `library_profiles`: identitas perpustakaan/sekolah.
-   `library_settings`: aturan sirkulasi.
-   `activity_logs`: audit aksi.
-   `import_batches`: metadata proses import.
-   `import_batch_rows`: staging data per baris.
-   `backup_logs`: riwayat backup.

## 5. Skema database yang disarankan

### users

-   id
-   name
-   email atau username (pilih satu strategi login dan gunakan
    konsisten)
-   password
-   role: `admin`, `petugas`
-   is_active
-   last_login_at
-   timestamps

### staff

-   id
-   user_id nullable unique FK users
-   staff_number unique
-   full_name
-   position nullable
-   phone nullable
-   is_active
-   timestamps

### classes

-   id
-   name
-   grade_level nullable
-   academic_year
-   timestamps
-   unique gabungan name + academic_year

### students

-   id
-   class_id nullable FK classes
-   student_number unique (NIS)
-   full_name
-   gender nullable
-   phone nullable
-   address nullable
-   status: `active`, `inactive`, `graduated`
-   timestamps

### teachers

-   id
-   teacher_number nullable unique
-   full_name
-   position nullable
-   phone nullable
-   address nullable
-   status: `active`, `inactive`
-   timestamps

### book_categories

-   id
-   name unique
-   description nullable
-   timestamps

### books

-   id
-   category_id FK
-   book_code unique
-   isbn nullable indexed
-   title
-   author nullable
-   publisher nullable
-   publication_year nullable
-   description nullable
-   timestamps

### book_copies

-   id
-   book_id FK
-   barcode unique
-   acquisition_date nullable
-   shelf_location nullable
-   condition: `good`, `minor_damage`, `major_damage`
-   status: `available`, `borrowed`, `damaged`, `lost`
-   timestamps
-   index book_id + status

### loans

-   id
-   transaction_code unique
-   student_id nullable FK
-   teacher_id nullable FK
-   processed_by FK users
-   borrowed_at
-   status: `active`, `partial`, `returned`, `cancelled`
-   cancelled_by nullable FK users
-   cancelled_at nullable
-   cancellation_reason nullable
-   notes nullable
-   timestamps
-   CHECK constraint: tepat satu dari student_id dan teacher_id terisi,
    jika didukung database

### loan_items

-   id
-   loan_id FK
-   book_copy_id FK
-   borrowed_at
-   due_date
-   returned_at nullable
-   returned_by nullable FK users
-   daily_fine_snapshot decimal
-   fine_amount decimal default 0
-   status: `borrowed`, `returned`, `cancelled`
-   timestamps
-   unique loan_id + book_copy_id
-   index status + due_date
-   index book_copy_id + status

### library_profiles

Simpan nama sekolah/perpustakaan, logo, alamat, kontak, dan data
akreditasi.

### library_settings

Simpan maksimal buku/peminjam, durasi pinjam, denda per hari, masa
tenggang, timezone, dan updated_by.

### activity_logs

Simpan user, action, module, description, subject_type/id, IP,
created_at. Log bersifat append-only dari aplikasi.

### import_batches dan import_batch_rows

Simpan jenis import, nama file, pengunggah, status, ringkasan jumlah
valid/invalid, data baris staging, dan kesalahan validasi.

### backup_logs

Simpan pembuat, nama/path privat, ukuran, jenis backup, status, pesan
kesalahan, dan waktu.

## 6. Relasi utama

-   User hasOne Staff (opsional).
-   Student belongsTo Class.
-   Book belongsTo BookCategory.
-   Book hasMany BookCopies.
-   Loan belongsTo Student atau Teacher.
-   Loan belongsTo User sebagai processor.
-   Loan hasMany LoanItems.
-   LoanItem belongsTo BookCopy.
-   LoanItem belongsTo User sebagai returned_by (opsional).

Hindari polymorphic relation untuk peminjam. Dua foreign key nullable
pada `loans` lebih mudah dijaga integritasnya dan lebih mudah dibuat
laporan SQL.

## 7. Status dan transisi

### Status eksemplar

-   `available` → `borrowed` ketika dipinjam.
-   `borrowed` → `available` ketika dikembalikan dalam kondisi baik.
-   `borrowed` → `damaged` jika dikembalikan rusak dan petugas memiliki
    izin menandai kondisi.
-   `available` → `damaged` atau `lost` melalui pengelolaan master yang
    diaudit.
-   Status `lost`/`damaged` tidak dihitung sebagai stok tersedia.

### Status loan item

-   `borrowed` → `returned`.
-   `borrowed` → `cancelled` hanya saat transaksi dibatalkan secara sah.
-   Item `returned` tidak boleh kembali menjadi `borrowed` lewat edit
    biasa.

### Status loan header

-   `active`: semua item masih dipinjam.
-   `partial`: sebagian item sudah kembali, sebagian masih dipinjam.
-   `returned`: semua item telah kembali.
-   `cancelled`: transaksi dibatalkan sesuai kebijakan.

Status header dihitung ulang dari status item setelah proses
return/cancel.

## 8. Service layer

Buat service/action terpisah, minimal: - `LoanService`: membuat
transaksi pinjam. - `ReturnService`: pengembalian parsial/penuh dan
hitung denda. - `LoanCancellationService`: pembatalan oleh Admin. -
`BarcodeService`: generate/render Code 128. - `ImportService`: parse,
preview, validate, commit. - `BackupService`: membuat backup dan
mencatat hasil.

Komponen Livewire bertugas menerima input, validasi antarmuka, memanggil
service, dan mengirim notifikasi. Hindari menumpuk logika bisnis pada
komponen.

## 9. Konsistensi transaksi dan concurrency

### Membuat pinjaman

Dalam satu `DB::transaction()`: 1. Kunci row peminjam (student atau
teacher) menggunakan `lockForUpdate()`. 2. Hitung jumlah item pinjaman
aktif milik peminjam. 3. Validasi batas maksimal. 4. Kunci seluruh
`book_copies` terpilih dengan `lockForUpdate()`. 5. Pastikan semua
eksemplar berstatus available. 6. Buat header loan. 7. Buat loan_items
dengan due date dan snapshot denda. 8. Ubah status eksemplar menjadi
borrowed. 9. Tulis activity log. 10. Commit.

Kunci row peminjam penting karena hanya mengunci eksemplar belum cukup
untuk mencegah dua transaksi bersamaan melewati batas maksimal pinjaman.

### Pengembalian

Dalam satu transaksi: 1. Kunci loan item dan book copy. 2. Pastikan item
masih borrowed. 3. Set returned_at dan returned_by. 4. Hitung hari
terlambat menurut kebijakan. 5. Hitung denda dari daily_fine_snapshot.
6. Perbarui status book copy sesuai kondisi. 7. Hitung ulang status
header loan. 8. Tulis activity log. 9. Commit.

### Pembatalan

Dalam satu transaksi: 1. Pastikan aktor Admin melalui Policy. 2. Kunci
loan dan item terkait. 3. Tolak jika ada item berstatus returned. 4.
Wajibkan alasan pembatalan. 5. Ubah item yang masih borrowed menjadi
cancelled. 6. Pulihkan status eksemplar yang masih dipinjam. 7. Set
cancelled_by, cancelled_at, cancellation_reason. 8. Tulis activity log.
9. Commit.

## 10. Struktur direktori yang disarankan

``` text
app/
  Livewire/
    Dashboard/
    Books/
    Students/
    Teachers/
    Staff/
    Circulation/
    Reports/
    Imports/
    Backups/
    Settings/
  Models/
  Policies/
  Services/
    LoanService.php
    ReturnService.php
    LoanCancellationService.php
    BarcodeService.php
    ImportService.php
    BackupService.php
  Support/
database/
  migrations/
  seeders/
  factories/
resources/
  views/
    layouts/
    components/
    livewire/
    print/
  scss/
    theme/
routes/
  web.php
tests/
  Feature/
  Unit/
storage/
  app/
    private/
      imports/
      backups/
```

Sesuaikan struktur dengan konvensi versi Laravel yang digunakan; jangan
memaksakan struktur jika starter project sudah memiliki pola lebih baik.

## 11. Deployment shared hosting

-   Pastikan versi PHP dan extension hosting memenuhi Laravel.
-   Arahkan document root domain/subdomain ke folder `public` Laravel
    jika hosting mengizinkan.
-   Jika tidak, gunakan prosedur deployment aman yang memisahkan folder
    publik dan aplikasi; jangan menaruh `.env`, `vendor`, atau storage
    privat di public web root.
-   Upload hasil build asset ke server; jangan mengandalkan Node.js
    berjalan di production.
-   Jalankan migration melalui SSH/terminal hosting jika tersedia. Jika
    tidak, buat prosedur deployment terbatas dan aman; jangan
    menyediakan endpoint migrasi publik.
-   Set `APP_ENV=production`, `APP_DEBUG=false`.
-   Set permission storage dan cache sesuai panduan hosting.
-   Jalankan `php artisan config:cache`, `route:cache`, dan `view:cache`
    jika sesuai.
-   Uji backup, restore, upload, download, dan session pada hosting.
-   Jangan menjanjikan fitur scheduler/queue jika paket hosting tidak
    mendukungnya.

## 12. Keputusan yang perlu dikonfirmasi sebelum rilis

-   Versi PHP dan Laravel yang tersedia di hosting.
-   Login menggunakan username atau email.
-   Apakah guru boleh meminjam dengan batas dan durasi berbeda.
-   Kebijakan denda final dan masa tenggang.
-   Apakah pengembalian rusak/hilang perlu proses pencatatan khusus.
-   Kebijakan import: commit baris valid saja atau seluruh batch harus
    valid.
-   Format nomor transaksi yang diinginkan.
