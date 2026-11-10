# PRD --- Sistem Informasi Perpustakaan Sekolah

-   Status: Draft dasar kebutuhan
-   Bahasa: Indonesia
-   Zona waktu: Asia/Jakarta
-   Target: aplikasi internal perpustakaan sekolah

## 1. Ringkasan produk

Sistem Informasi Perpustakaan Sekolah (SIPUS) adalah aplikasi web
internal untuk mengelola katalog buku, eksemplar fisik, data siswa dan
guru, petugas, peminjaman, pengembalian, laporan, import/export, dan
backup.

Aplikasi dipakai oleh beberapa Admin/Petugas dari komputer berbeda
melalui browser. Fokus utama adalah operasional harian yang cepat,
pencatatan transaksi yang akurat, dan histori yang dapat diaudit.

## 2. Tujuan

1.  Menggantikan pencatatan perpustakaan manual/spreadsheet yang
    tersebar.
2.  Mengetahui jumlah judul, eksemplar, dan stok tersedia secara akurat.
3.  Mempercepat transaksi pinjam dan kembali.
4.  Mengurangi risiko satu eksemplar dipinjam oleh dua orang pada waktu
    bersamaan.
5.  Memisahkan data siswa dan guru.
6.  Menyediakan laporan operasional dan backup data.

## 3. Pengguna dan hak akses

### Admin

Admin memiliki akses penuh: - Dashboard dan seluruh laporan. - Tambah,
lihat, ubah, dan deaktivasi data siswa, guru, petugas, kategori, buku,
dan eksemplar. - Kelola akun pengguna dan role. - Atur profil
perpustakaan dan aturan peminjaman. - Input/ubah transaksi sesuai
kebijakan. - Melakukan pengembalian. - Melihat seluruh transaksi. -
Membatalkan transaksi dengan alasan. - Import/export data. - Membuat dan
mengunduh backup. - Melihat activity log.

### Petugas

Petugas memiliki akses operasional: - Melihat dashboard operasional. -
Input dan edit data siswa/guru. - Input dan edit data
buku/kategori/eksemplar sesuai izin operasional. - Input transaksi
pinjam dan pengembalian. - Melihat anggota dan laporan operasional. -
Melihat transaksi yang diperlukan untuk pelayanan.

Petugas tidak boleh: - Mengelola akun pengguna dan role. - Mengubah
pengaturan global perpustakaan. - Membatalkan transaksi. - Menghapus
permanen master data. - Mengakses backup privat atau mengubah log.

Catatan: batasan akses harus diterapkan di backend, bukan hanya
menyembunyikan menu.

## 4. Ruang lingkup MVP

### 4.1 Autentikasi dan pengguna

-   Login/logout.
-   Akun hanya untuk Admin/Petugas.
-   Status akun aktif/nonaktif.
-   Role Admin dan Petugas.
-   Admin dapat mengelola akun.
-   Petugas tidak dapat mengelola akun.

### 4.2 Dashboard

Tampilkan: - Jumlah judul buku. - Jumlah seluruh eksemplar. - Jumlah
eksemplar tersedia. - Jumlah eksemplar sedang dipinjam. - Jumlah siswa
aktif. - Jumlah guru aktif. - Jumlah petugas aktif. - Lima transaksi
terbaru. - Profil perpustakaan/sekolah. - Status akreditasi jika datanya
tersedia.

Dashboard tidak boleh menampilkan angka stok yang tidak konsisten dengan
tabel eksemplar.

### 4.3 Katalog buku

-   Daftar judul buku.
-   Pencarian judul, penulis, kode buku, ISBN.
-   Filter kategori.
-   Informasi penerbit, tahun, deskripsi, kategori.
-   Informasi lokasi rak.
-   Kelola eksemplar fisik.
-   Import/export katalog.
-   Cetak katalog.
-   Barcode Code 128 unik untuk setiap eksemplar.
-   Cetak label barcode.

Data judul dan eksemplar harus dipisahkan. Satu judul dapat memiliki
banyak eksemplar.

### 4.4 Data siswa

Data siswa disimpan terpisah dari guru: - NIS (unik). - Nama. - Kelas. -
Jenis kelamin (opsional). - Kontak/telepon (opsional). - Alamat
(opsional). - Status: aktif, nonaktif, lulus.

Kelas memiliki nama/tingkat dan tahun ajaran. Perubahan kelas tidak
boleh merusak histori pinjaman lama.

### 4.5 Data guru

Data guru disimpan terpisah dari siswa: - NIP/nomor identitas (opsional,
unik jika diisi). - Nama lengkap. - Jabatan. - Telepon (opsional). -
Alamat (opsional). - Status aktif/nonaktif.

### 4.6 Data petugas

-   Nomor identitas/NIP.
-   Nama lengkap.
-   Jabatan.
-   Kontak.
-   Status aktif/nonaktif.
-   Opsional relasi ke akun login.

### 4.7 Sirkulasi peminjaman

-   Pencarian peminjam berdasarkan siswa atau guru.
-   Peminjam dalam satu transaksi harus tepat satu jenis: siswa atau
    guru.
-   Satu transaksi dapat berisi beberapa eksemplar.
-   Pemilihan buku menggunakan pencarian; item terpilih ditampilkan
    sebagai tag yang dapat dilepas.
-   Validasi stok tersedia.
-   Validasi batas maksimal pinjaman per peminjam.
-   Tanggal pinjam otomatis mengikuti waktu aplikasi.
-   Jatuh tempo dihitung dari pengaturan durasi pinjam.
-   Nomor transaksi unik dan mudah dibaca.
-   Simpan petugas pemroses.
-   Stok tersedia berkurang otomatis melalui perubahan status eksemplar.

### 4.8 Pengembalian

-   Daftar transaksi aktif dan terlambat.
-   Pengembalian dapat dilakukan per item, tidak harus seluruh transaksi
    sekaligus.
-   Item yang dikembalikan mengubah status eksemplar menjadi tersedia
    (kecuali petugas menandai rusak/hilang melalui alur yang disetujui).
-   Sistem menghitung keterlambatan dan denda jika denda aktif.
-   Status transaksi menjadi `partial` jika masih ada item dipinjam dan
    minimal satu item sudah kembali; `returned` jika seluruh item
    selesai.
-   Simpan waktu dan petugas penerima pengembalian.

### 4.9 Pengaturan

Pengaturan minimum: - Maksimal buku per peminjam (default 3). - Lama
pinjam dalam hari (default 7). - Denda per hari (default Rp0). - Masa
tenggang (default 0 hari). - Zona waktu (default Asia/Jakarta).

Perubahan pengaturan hanya berlaku untuk transaksi baru, kecuali ada
kebijakan eksplisit. Nilai denda per hari harus disalin ke item pinjaman
sebagai snapshot saat transaksi dibuat.

### 4.10 Laporan

Filter bulan dan tahun, serta filter lain jika relevan: - Transaksi
peminjaman. - Pengembalian. - Pinjaman aktif. - Pinjaman terlambat. -
Anggota aktif. - Katalog buku. - Rekap peminjaman per siswa/guru. -
Ekspor CSV dan JSON. - Tampilan siap cetak.

### 4.11 Import, export, dan backup

-   Import siswa, guru, dan buku.
-   Preview isi file sebelum penyimpanan.
-   Validasi kolom wajib, format, duplikasi, relasi, dan barcode.
-   Tampilkan jumlah baris valid dan invalid serta alasan kesalahan.
-   Admin memilih kebijakan untuk melanjutkan hanya baris valid atau
    membatalkan seluruh batch; tidak boleh mengimpor diam-diam.
-   Export JSON/CSV.
-   Backup database dan/atau data aplikasi.
-   Catat riwayat backup.
-   File backup disimpan di lokasi privat, bukan public web root.

## 5. Aturan bisnis rinci

### 5.1 Identitas peminjam

Tabel siswa dan guru terpisah. Pada header transaksi, gunakan
`student_id` atau `teacher_id`, tepat satu yang terisi. Validasi
dilakukan di Laravel dan, jika versi database mendukung secara
konsisten, melalui CHECK constraint.

### 5.2 Eksemplar dan stok

-   Setiap eksemplar memiliki barcode unik.
-   Status eksemplar: `available`, `borrowed`, `damaged`, `lost`.
-   Jumlah tersedia = jumlah eksemplar dengan status `available`.
-   Eksemplar yang rusak/hilang tidak dihitung tersedia.
-   Buku/judul yang memiliki histori transaksi tidak boleh dihapus
    permanen.

### 5.3 Peminjaman

-   Setiap `loan_item` mengacu pada satu eksemplar fisik.
-   Tidak boleh ada eksemplar yang memiliki dua item pinjaman aktif.
-   Semua validasi stok dan batas pinjam dilakukan dalam transaksi
    database.
-   Gunakan row lock agar dua petugas tidak dapat meminjamkan eksemplar
    yang sama secara bersamaan.
-   Untuk batas pinjaman, serialisasikan pengecekan per peminjam dengan
    mengunci row stabil milik peminjam sebelum menghitung pinjaman
    aktif.

### 5.4 Pengembalian parsial

Item dikembalikan satu per satu. Histori tanggal, petugas, dan denda
item tidak boleh hilang saat item lain dalam transaksi masih dipinjam.

### 5.5 Pembatalan

-   Hanya Admin.
-   Wajib mengisi alasan.
-   Tidak boleh membatalkan transaksi yang sudah memiliki item
    dikembalikan.
-   Saat dibatalkan, item yang masih dipinjam menjadi cancelled dan
    eksemplarnya dikembalikan ke available sesuai kondisi aktual.
-   Catat aktor dan waktu pembatalan.
-   Jangan menghapus transaksi.

### 5.6 Denda

-   Default Rp0 (nonaktif secara praktis).
-   Jika diaktifkan, hitung berdasarkan hari keterlambatan setelah masa
    tenggang.
-   Simpan `daily_fine_snapshot` dan `fine_amount` per item.
-   Aturan pembulatan dan apakah hari kalender atau hari operasional
    perlu diputuskan sebelum implementasi final; MVP menggunakan hari
    kalender.

## 6. Kebutuhan nonfungsional

-   Ringan dan responsif pada shared hosting.
-   Multi-user melalui browser.
-   Aman terhadap akses lintas role.
-   Data transaksi konsisten walau ada akses bersamaan.
-   Tabel besar menggunakan pagination dan filter server-side.
-   UI dapat dipakai pada laptop dan tablet.
-   Waktu aplikasi konsisten Asia/Jakarta.
-   Backup dan restore dapat diuji.
-   Tidak memerlukan websocket/server khusus untuk MVP; Livewire request
    cukup untuk interaksi yang diperlukan.

## 7. Di luar ruang lingkup MVP

-   Portal siswa/orang tua.
-   Login publik atau self-registration.
-   Notifikasi WhatsApp/SMS.
-   Integrasi barcode scanner khusus selain input keyboard scanner.
-   RFID.
-   Pembayaran denda online.
-   Aplikasi Android/iOS native.
-   Sinkronisasi offline.
-   Integrasi sistem akademik eksternal.

## 8. Kriteria penerimaan MVP

1.  Admin dan Petugas dapat login sesuai role.
2.  Petugas tidak dapat mengakses URL aksi Admin secara langsung.
3.  Siswa dan guru tersimpan pada tabel berbeda.
4.  Satu judul dapat memiliki beberapa eksemplar dengan barcode unik.
5.  Peminjaman multi-eksemplar tersimpan sebagai satu transaksi.
6.  Eksemplar tidak dapat dipinjam dua kali secara bersamaan.
7.  Batas maksimal pinjaman tidak dapat dilewati melalui dua request
    bersamaan.
8.  Pengembalian parsial memperbarui status item, eksemplar, dan header
    transaksi dengan benar.
9.  Denda Rp0 tidak menghasilkan tagihan.
10. Pembatalan hanya dapat dilakukan Admin dan memiliki alasan.
11. Import menampilkan preview dan kesalahan sebelum menyimpan.
12. Laporan dapat difilter dan diekspor.
13. Backup tidak dapat diakses tanpa otorisasi.
14. Test fitur utama lulus.
