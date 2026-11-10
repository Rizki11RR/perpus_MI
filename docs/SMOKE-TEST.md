# Smoke test SIPUS

Environment external wajib menjalankan daftar ini sebelum rilis.

## Desktop dan tablet

- Login valid dan invalid.
- Logout.
- Dashboard empty dan berisi data.
- CRUD kategori, buku, eksemplar, kelas, siswa, guru, petugas.
- Search, filter, pagination, empty, loading, validation, error.
- SweetAlert hapus.
- Pinjam siswa dan guru.
- Multi-eksemplar dan hapus tag.
- Pengembalian parsial dan penuh.
- Pembatalan Admin, alasan wajib, Petugas ditolak.
- Laporan, filter, CSV, JSON, print.
- Import preview, invalid rows, commit, cancel.
- Backup create, verified status, download Admin, Petugas ditolak.

## Bukti

Catat URL, akun uji, viewport, hasil, waktu, dan console error untuk setiap item.

## Tidak boleh diklaim PASS

Jika browser, printer, shared-hosting staging, atau restore staging belum tersedia, item tersebut tetap BLOCKED.
