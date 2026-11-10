<div>
    <div class="mb-4"><h2 class="h4 mb-1">Ringkasan operasional</h2><p class="text-muted mb-0">Jumlah dihitung langsung dari data perpustakaan.</p></div>
    <div class="row g-3 mb-4">
        @foreach([['Judul buku',$bookTitles],['Seluruh eksemplar',$totalCopies],['Eksemplar tersedia',$availableCopies],['Sedang dipinjam',$borrowedCopies],['Siswa aktif',$activeStudents],['Guru aktif',$activeTeachers],['Petugas aktif',$activeStaff]] as [$label,$value])<div class="col-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="small text-muted">{{$label}}</div><div class="fs-3 fw-semibold text-primary">{{$value}}</div></div></div></div>@endforeach
    </div>
    <div class="card"><div class="card-body"><h3 class="h5">Lima transaksi terbaru</h3><div class="table-responsive"><table class="table"><thead><tr><th>Kode</th><th>Peminjam</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>@forelse($recentLoans as $loan)<tr><td>{{$loan->transaction_code}}</td><td>{{$loan->student?->full_name ?: $loan->teacher?->full_name}}</td><td>{{$loan->borrowed_at->format('d/m/Y H:i')}}</td><td>{{$loan->status}}</td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-4">Belum ada transaksi peminjaman.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
