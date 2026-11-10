# AGENTS.md

## Instruksi utama untuk AI coding agent

Dokumen ini adalah aturan kerja utama bagi AI agent yang mengembangkan
**Sistem Informasi Perpustakaan Sekolah (SIPUS)**. Baca dokumen ini
sebelum mengubah kode. Dokumen kebutuhan dan keputusan rinci berada di
`PRD.md`, `ARCHITECTURE.md`, `DESIGN.md`, `TASK.md`, dan `PROGRESS.md`.

## 1. Tujuan proyek

Bangun aplikasi web perpustakaan sekolah yang ringan, mudah digunakan
oleh beberapa petugas dari komputer berbeda, aman untuk data transaksi,
dan dapat dijalankan pada shared hosting.

Aplikasi hanya digunakan oleh Admin dan Petugas yang login. Tidak ada
portal publik/peminjam pada tahap awal.

## 2. Sumber kebenaran dan urutan prioritas

Jika ada instruksi yang bertentangan, gunakan urutan berikut: 1.
Instruksi eksplisit terbaru dari pemilik proyek. 2. `PRD.md` untuk
kebutuhan dan aturan bisnis. 3. `ARCHITECTURE.md` untuk keputusan teknis
dan model data. 4. `DESIGN.md` untuk UI/UX dan sistem desain. 5.
`TASK.md` untuk urutan implementasi. 6. `PROGRESS.md` untuk status
pekerjaan aktual.

Jangan mengubah keputusan bisnis penting secara diam-diam. Jika
menemukan ambiguitas yang berdampak pada data, keamanan, atau alur
transaksi, jelaskan dan minta keputusan sebelum implementasi.

## 3. Stack dan batasan teknis

-   Backend: Laravel.
-   UI interaktif: Livewire.
-   Database: MySQL/MariaDB dengan InnoDB.
-   CSS/UI: Bootstrap 5 berbasis template Mazer.
-   Build asset: Vite sesuai konfigurasi Laravel dan versi Mazer yang
    benar-benar digunakan.
-   Bahasa antarmuka: Bahasa Indonesia.
-   Zona waktu aplikasi: `Asia/Jakarta`.
-   Target deployment: shared hosting/cPanel.
-   Pengembangan utama: lokal di Linux Mint.

Jangan mengganti stack, menambahkan SPA framework, atau memasukkan
layanan infrastruktur yang membutuhkan VPS tanpa persetujuan.

### Versi dependency

Jangan mengarang versi Laravel, Livewire, PHP, Bootstrap, atau Mazer.
Periksa `composer.json`, `package.json`, lockfile, dan versi PHP target
sebelum memasang dependency. Pilih kombinasi versi yang kompatibel
dengan shared hosting. Catat keputusan versi di `ARCHITECTURE.md`.

## 4. Aturan kerja agent

1.  Baca dokumen proyek dan periksa struktur repository sebelum coding.
2.  Kerjakan satu task atau kelompok task kecil yang saling terkait.
3.  Sebelum mengubah file, pahami pola yang sudah ada. Hindari membuat
    pola kedua untuk masalah yang sama.
4.  Jangan menimpa pekerjaan yang sudah ada tanpa memeriksa dampaknya.
5.  Jangan membuat fitur hanya berupa tampilan statis jika task meminta
    alur yang berfungsi.
6.  Jangan menganggap proses berhasil sebelum menjalankan pemeriksaan
    yang relevan.
7.  Setelah perubahan, jalankan test, lint/build, atau pemeriksaan
    manual yang sesuai.
8.  Perbarui `TASK.md` dan `PROGRESS.md` setelah pekerjaan selesai atau
    terhalang.
9.  Jika menemukan bug di luar task, catat di `PROGRESS.md`; jangan
    melakukan refactor besar tanpa alasan.
10. Tampilkan ringkasan file yang diubah, hasil pengujian, dan pekerjaan
    tersisa pada akhir sesi.

## 5. Standar coding

-   Ikuti konvensi Laravel dan PSR-12.
-   Gunakan Form Request atau validasi Livewire yang terstruktur.
-   Gunakan Policy/Gate atau middleware untuk otorisasi; jangan hanya
    menyembunyikan tombol.
-   Letakkan aturan transaksi sirkulasi di service/action class, bukan
    seluruhnya di komponen Livewire.
-   Gunakan Eloquent relationship dan eager loading untuk menghindari
    N+1.
-   Gunakan database transaction untuk proses pinjam, kembali,
    pembatalan, dan import.
-   Gunakan `lockForUpdate()` pada proses sirkulasi yang rawan race
    condition.
-   Gunakan pagination untuk tabel data.
-   Gunakan soft delete/deaktivasi hanya jika sesuai kebutuhan; jangan
    hard-delete histori transaksi.
-   Hindari query mentah jika Eloquent cukup, tetapi gunakan query
    builder ketika lebih jelas/efisien.
-   Jangan menyimpan file backup di direktori publik.
-   Jangan menaruh credential, token, password, atau data rahasia di
    repository.
-   Jangan membuat akun admin dengan password tetap yang diketahui
    publik. Gunakan seeder lokal yang meminta konfigurasi aman atau
    proses setup awal.

## 6. Aturan bisnis yang tidak boleh dilanggar

-   Data siswa dan guru terpisah sepenuhnya.
-   Setiap eksemplar fisik buku memiliki barcode unik.
-   `books` adalah data judul/bibliografi; `book_copies` adalah
    eksemplar fisik.
-   Stok tersedia dihitung dari eksemplar berstatus tersedia, bukan
    angka stok manual yang diedit.
-   Satu transaksi pinjam dapat memuat beberapa eksemplar buku.
-   Pengembalian parsial diperbolehkan.
-   Denda bersifat opsional dan nilai awalnya Rp0.
-   Nilai denda harian pada transaksi harus disimpan sebagai snapshot
    agar histori tidak berubah ketika pengaturan diganti.
-   Hanya Admin yang boleh membatalkan transaksi.
-   Pembatalan tidak boleh menghapus jejak transaksi.
-   Transaksi yang sudah memiliki pengembalian parsial tidak boleh
    dibatalkan; sisa item harus diselesaikan melalui pengembalian
    normal.
-   Sebelum menyimpan import, tampilkan preview dan validasi.
-   Petugas tidak boleh mengelola akun pengguna atau pengaturan sistem.
-   Data yang sudah direferensikan transaksi tidak boleh dihapus secara
    permanen.

## 7. Keamanan

-   Semua halaman aplikasi (kecuali login dan halaman teknis yang memang
    diperlukan) wajib terautentikasi.
-   Terapkan otorisasi pada server untuk setiap operasi.
-   Validasi semua input, termasuk ID yang dikirim dari browser.
-   Gunakan CSRF protection bawaan Laravel.
-   Lindungi upload berdasarkan MIME, ekstensi, ukuran, dan lokasi
    penyimpanan.
-   Batasi percobaan login jika tersedia pada stack yang dipilih.
-   Jangan menampilkan stack trace atau detail database pada production.
-   Catat aksi penting: login (secukupnya), perubahan master,
    peminjaman, pengembalian, pembatalan, import, dan backup.
-   Jangan mencatat password, token, atau data sensitif ke activity log.
-   Backup harus disimpan di storage privat dan hanya dapat diunduh oleh
    Admin yang berwenang.

## 8. Standar UX

-   Gunakan Bahasa Indonesia yang jelas dan konsisten.
-   Form CRUD menggunakan modal jika sesuai dengan kompleksitas form.
-   Hapus data harus memakai SweetAlert2 untuk konfirmasi, bukan
    `window.confirm()`.
-   Berhasil/gagal menggunakan toast atau alert yang konsisten.
-   Jangan mengandalkan warna saja untuk menyampaikan status; sertakan
    teks/badge.
-   Tabel harus memiliki pencarian, filter yang relevan, pagination,
    empty state, dan loading state.
-   Form harus menampilkan validasi dekat dengan field terkait.
-   Input buku pada transaksi pinjam harus mendukung pencarian dan
    pemilihan banyak item seperti searchable tag input; item terpilih
    dapat dihapus sebelum transaksi disimpan.
-   Hindari refresh halaman penuh untuk interaksi yang dapat ditangani
    Livewire.
-   Pastikan layout tetap dapat digunakan pada layar laptop dan tablet.

## 9. Kualitas dan pengujian

Minimal buat test untuk: - Login dan akses role. - CRUD master data dan
batasan role. - Barcode unik. - Peminjaman multi-eksemplar. - Pencegahan
peminjaman eksemplar yang sama secara bersamaan. - Batas maksimal
pinjaman per peminjam. - Pengembalian parsial dan penuh. - Perhitungan
denda dengan snapshot. - Pembatalan hanya oleh Admin dan hanya pada
kondisi yang diizinkan. - Import preview, validasi, dan penyimpanan. -
Proteksi unduhan backup.

Jangan menandai task selesai hanya karena halaman berhasil dibuka. Uji
perilaku dan aturan bisnisnya.

## 10. Cara memperbarui dokumen

Setelah setiap task: - Ubah checkbox task yang benar-benar selesai di
`TASK.md`. - Tambahkan tanggal, ringkasan, file penting, test, dan
blocker di `PROGRESS.md`. - Jika keputusan arsitektur berubah, perbarui
`ARCHITECTURE.md` dan jelaskan alasannya. - Jika perilaku yang terlihat
pengguna berubah, perbarui `PRD.md` atau `DESIGN.md` jika relevan.

## 11. Definisi selesai

Sebuah task dianggap selesai jika: - Implementasi sesuai acceptance
criteria. - Validasi dan otorisasi server tersedia. - Tidak merusak
fitur yang sudah ada. - Test relevan lulus atau keterbatasan test
dijelaskan. - Tidak ada error build yang diperkenalkan. - Dokumentasi
progres diperbarui.
