{{-- Tujuan: menampilkan dan mengelola katalog buku, termasuk import dan export CSV.
Caller: route catalog.books melalui komponen Livewire BookIndex.
Dependensi: BookIndex, MasterDataCsvService, dan route export CSV.
Public utama: CRUD buku, pencarian, filter, import CSV, export CSV, pagination.
Side effect: upload CSV, perubahan buku, dan download CSV. --}}
<div>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Katalog buku</h2></div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif

    <div class="card card-body mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-end">
            <a class="btn btn-outline-secondary" href="{{ route('catalog.books.export') }}">Export CSV</a>
            <div><label class="form-label mb-1" for="bookImportFile">File CSV buku</label><input id="bookImportFile" type="file" class="form-control" wire:model="importFile" accept=".csv,.txt">@error('importFile')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <button class="btn btn-outline-primary" type="button" wire:click="importCsv" wire:loading.attr="disabled" wire:target="importCsv">Import CSV</button>
        </div>
        <div wire:loading wire:target="importCsv" class="small text-muted mt-2" role="status">Mengimport...</div>
    </div>

    <form wire:submit="save" class="card card-body mb-3">
        <div wire:loading wire:target="save" class="small text-muted mb-2" role="status">Menyimpan...</div>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label" for="title">Judul</label><input id="title" class="form-control @error('title')is-invalid @enderror" wire:model="title" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label class="form-label" for="bookCode">Kode buku</label><input id="bookCode" class="form-control" wire:model="bookCode" required></div>
            <div class="col-md-3"><label class="form-label" for="categoryId">Kategori</label><select id="categoryId" class="form-select" wire:model="categoryId" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-2 d-flex align-items-end gap-2"><button class="btn btn-primary" type="submit">{{ $editingId ? 'Perbarui' : 'Tambah' }}</button>@if($editingId)<button class="btn btn-outline-secondary" type="button" wire:click="resetForm">Batal</button>@endif</div>
            <div class="col-md-4"><label class="form-label" for="author">Penulis</label><input id="author" class="form-control" wire:model="author"></div>
            <div class="col-md-4"><label class="form-label" for="publisher">Penerbit</label><input id="publisher" class="form-control" wire:model="publisher"></div>
            <div class="col-md-2"><label class="form-label" for="publicationYear">Tahun</label><input id="publicationYear" type="number" class="form-control" wire:model="publicationYear"></div>
            <div class="col-md-2"><label class="form-label" for="isbn">ISBN</label><input id="isbn" class="form-control" wire:model="isbn"></div>
        </div>
    </form>

    <div class="card card-body">
        <div class="row g-2 mb-3"><div class="col-md-8"><label class="form-label" for="search">Cari katalog</label><input id="search" class="form-control" wire:model.live.debounce.300ms="search"></div><div class="col-md-4"><label class="form-label" for="filter">Filter kategori</label><select id="filter" class="form-select" wire:model.live="categoryFilter"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div></div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Kode</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Aksi</th></tr></thead><tbody>
            @forelse($books as $book)<tr><td>{{ $book->book_code }}</td><td>{{ $book->title }}</td><td>{{ $book->category?->name ?: '-' }}</td><td>{{ $book->author ?: '-' }}</td><td><button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $book->id }})">Ubah</button> <button class="btn btn-sm btn-outline-danger" data-confirm-delete wire:click="delete({{ $book->id }})">Hapus</button> <button class="btn btn-sm btn-outline-warning" wire:click="deactivate({{ $book->id }})">Nonaktifkan</button></td></tr>
            @empty<tr><td colspan="5" class="text-center py-4">Belum ada buku.</td></tr>@endforelse
        </tbody></table></div>
        {{ $books->links() }}
    </div>
</div>
