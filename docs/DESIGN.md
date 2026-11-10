# DESIGN --- UI/UX dan Design System SIPUS

## 1. Arah visual

Gunakan template **Mazer** sebagai dasar layout dan komponen. Identitas
visual aplikasi adalah dashboard perpustakaan sekolah yang bersih,
tenang, profesional, dan memiliki nuansa hijau Islami.

Nuansa Islami diterapkan melalui warna hijau, keteraturan, dan
kesederhanaan. Hindari ornamen religius berlebihan, motif dekoratif yang
mengganggu, atau penggunaan simbol agama sebagai hiasan tanpa kebutuhan.

Referensi: - Mazer: https://github.com/zuramai/mazer - Dokumentasi
kustomisasi Mazer: https://zuramai.github.io/mazer/docs/customizing.html

## 2. Palet warna hijau Islami

Gunakan token warna berikut sebagai palet awal. Implementasikan sebagai
CSS custom properties/SCSS variables, lalu petakan ke Bootstrap theme
colors dan komponen Mazer.

  ------------------------------------------------------------------------
  Token                   Hex                     Penggunaan
  ----------------------- ----------------------- ------------------------
  Primary / Islamic Green `#166534`               Tombol utama, link
                                                  aktif, elemen brand

  Primary Dark            `#14532D`               Hover/active, sidebar
                                                  gelap

  Primary Deep            `#064E3B`               Heading/aksen kuat

  Primary Soft            `#DCFCE7`               Background badge dan
                                                  highlight

  Secondary / Sage        `#84A98C`               Aksen sekunder

  Accent / Gold           `#D4A72C`               Aksen kecil, status
                                                  unggulan

  App Background          `#F5F8F5`               Latar halaman

  Surface                 `#FFFFFF`               Card, modal, tabel

  Text Primary            `#1F2937`               Teks utama

  Text Muted              `#64748B`               Label sekunder

  Border                  `#E2E8F0`               Garis pembatas

  Success                 `#15803D`               Berhasil/tersedia

  Warning                 `#D97706`               Menunggu/peringatan

  Danger                  `#DC2626`               Error/hilang/terlambat

  Info                    `#0F766E`               Informasi
  ------------------------------------------------------------------------

### Contoh token CSS

``` css
:root {
  --sipus-primary: #166534;
  --sipus-primary-dark: #14532D;
  --sipus-primary-deep: #064E3B;
  --sipus-primary-soft: #DCFCE7;
  --sipus-secondary: #84A98C;
  --sipus-accent: #D4A72C;
  --sipus-bg: #F5F8F5;
  --sipus-surface: #FFFFFF;
  --sipus-text: #1F2937;
  --sipus-muted: #64748B;
  --sipus-border: #E2E8F0;
}
```

Pastikan kontras teks dan background memadai. Jangan menggunakan hijau
muda untuk teks kecil di atas putih. Tombol utama memakai teks putih di
atas primary green.

## 3. Layout aplikasi

### Desktop

-   Sidebar kiri menggunakan struktur Mazer.
-   Navbar atas untuk breadcrumb, identitas pengguna, dan logout.
-   Area konten memakai lebar fleksibel.
-   Dashboard memakai grid card ringkas.
-   Tabel menggunakan card/container yang konsisten.
-   Sidebar dapat collapse sesuai kemampuan template.

### Tablet

-   Sidebar dapat collapse/off-canvas.
-   Tabel tetap dapat di-scroll horizontal jika kolom banyak.
-   Tombol aksi utama tetap mudah dijangkau.

### Halaman login

-   Form sederhana dan fokus.
-   Branding nama perpustakaan/sekolah.
-   Hindari elemen dekoratif berat.

## 4. Navigasi

Susunan menu yang disarankan:

1.  Dashboard
2.  Data Master
    -   Katalog Buku
    -   Kategori
    -   Eksemplar / Barcode
    -   Data Siswa
    -   Data Guru
    -   Data Petugas
3.  Sirkulasi
    -   Peminjaman
    -   Pengembalian
    -   Riwayat Transaksi
4.  Laporan
5.  Import / Export
6.  Backup
7.  Pengaturan
8.  Manajemen Pengguna (Admin saja)

Menu harus disesuaikan dengan role. Namun otorisasi backend tetap wajib.

## 5. Komponen dan pola interaksi

### Button

-   Primary: hijau utama.
-   Secondary: outline/abu.
-   Danger: merah untuk aksi destruktif.
-   Semua tombol memiliki label jelas dan state loading/disabled jika
    sedang memproses.

### Form

-   Label di atas input.
-   Tampilkan required marker bila wajib.
-   Pesan validasi dekat input.
-   Gunakan helper text untuk format NIS, barcode, atau tanggal.
-   Pertahankan input saat validasi gagal.

### Modal CRUD

-   Gunakan modal untuk tambah/edit sederhana.
-   Form panjang dapat menggunakan halaman khusus.
-   Modal tidak boleh menutup ketika proses simpan masih berjalan.
-   Reset state ketika modal dibuka untuk record baru.

### Delete confirmation

-   SweetAlert2.
-   Jelaskan data yang akan dihapus/nonaktifkan.
-   Untuk data yang memiliki histori, gunakan deaktivasi, bukan hard
    delete.
-   Jangan gunakan `window.confirm()`.

### Toast

-   Sukses: hijau.
-   Error: merah.
-   Warning: amber.
-   Pesan singkat dan menyebut tindakan yang berhasil/gagal.

### Table

-   Header jelas.
-   Search dan filter di atas tabel.
-   Pagination server-side.
-   Empty state dengan instruksi tindakan.
-   Badge status dengan teks.
-   Aksi row tidak berlebihan.
-   Tampilkan loading indicator saat request Livewire.

### Multi-book picker

-   Input pencarian buku/eksemplar.
-   Hasil pencarian menampilkan judul, barcode/kode, dan status
    ketersediaan.
-   Klik untuk menambahkan item.
-   Item terpilih ditampilkan sebagai removable tags/list.
-   Tidak boleh memilih item yang sama dua kali.
-   Item tidak tersedia tidak dapat dipilih.
-   Sediakan tombol hapus per tag.

## 6. Status visual

  Status      Warna         Label contoh
  ----------- ------------- ------------------
  Available   Hijau         Tersedia
  Borrowed    Biru/teal     Dipinjam
  Returned    Hijau         Dikembalikan
  Partial     Amber         Sebagian kembali
  Overdue     Merah         Terlambat
  Damaged     Abu/merah     Rusak
  Lost        Merah gelap   Hilang
  Cancelled   Abu           Dibatalkan
  Active      Hijau         Aktif
  Inactive    Abu           Nonaktif

Selalu sertakan label teks, jangan hanya mengandalkan warna.

## 7. Dashboard

Prioritaskan ringkasan yang membantu petugas: - Total judul buku. -
Total eksemplar. - Eksemplar tersedia. - Eksemplar dipinjam. - Siswa
aktif. - Guru aktif. - Petugas aktif. - Lima transaksi terakhir.

Gunakan card sederhana, angka mudah dibaca, icon konsisten, dan jangan
terlalu banyak chart. Grafik hanya ditambahkan jika memberi insight
operasional yang jelas.

## 8. Halaman cetak

Buat layout print terpisah atau print stylesheet: - Katalog. - Label
barcode Code 128. - Laporan transaksi. - Laporan keterlambatan. -
Laporan pengembalian.

Gunakan `@media print` untuk menyembunyikan sidebar, navbar, tombol,
filter, dan elemen interaktif. Pastikan ukuran label barcode dapat
dikonfigurasi dan hasil cetak diuji pada printer nyata.

## 9. Aksesibilitas dan responsivitas

-   Kontras teks memadai.
-   Input memiliki label.
-   Tombol memiliki teks atau accessible label.
-   Navigasi dapat digunakan dengan keyboard.
-   Focus state terlihat.
-   Jangan menyampaikan status hanya dengan warna.
-   Hindari font terlalu kecil.
-   Uji minimal pada lebar laptop dan tablet.

## 10. Aturan implementasi Mazer

-   Gunakan komponen dan utility Mazer/Bootstrap yang sudah tersedia.
-   Jangan menyalin halaman demo utuh lalu mengganti teks saja.
-   Hindari CSS override yang terlalu luas.
-   Simpan custom theme dalam file terpisah.
-   Jangan mengedit file hasil compile jika source SCSS tersedia.
-   Periksa versi Bootstrap dan struktur aktual repository sebelum
    mengikuti contoh dokumentasi lama.
-   Jangan memasang jQuery; Mazer menyatakan tidak bergantung pada
    jQuery.
