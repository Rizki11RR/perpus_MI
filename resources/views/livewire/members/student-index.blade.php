{{-- Tujuan: menampilkan dan mengelola data siswa, termasuk import dan export CSV.
Caller: route data.students melalui komponen Livewire StudentIndex.
Dependensi: StudentIndex, MasterDataCsvService, dan route export CSV.
Public utama: CRUD siswa, pencarian, import CSV, export CSV, pagination.
Side effect: upload CSV, perubahan siswa, dan download CSV. --}}
<div>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Data siswa</h2></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card card-body mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-end">
            <a class="btn btn-outline-secondary" href="{{ route('data.students.export') }}">Export CSV</a>
            <div>
                <label class="form-label mb-1" for="studentImportFile">File CSV siswa</label>
                <input id="studentImportFile" type="file" class="form-control" wire:model="importFile" accept=".csv,.txt">
                @error('importFile')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-outline-primary" type="button" wire:click="importCsv" wire:loading.attr="disabled" wire:target="importCsv">Import CSV</button>
        </div>
        <div wire:loading wire:target="importCsv" class="small text-muted mt-2" role="status">Mengimport...</div>
    </div>

    <form wire:submit="save" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label" for="nis">NIS</label><input id="nis" class="form-control" wire:model="studentNumber" required></div>
            <div class="col-md-4"><label class="form-label" for="studentName">Nama</label><input id="studentName" class="form-control" wire:model="fullName" required></div>
            <div class="col-md-3"><label class="form-label" for="studentClass">Kelas</label><select id="studentClass" class="form-select" wire:model="classId"><option value="">Tanpa kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary" type="submit">Simpan</button></div>
        </div>
    </form>

    <div class="card card-body">
        <input class="form-control mb-3" placeholder="Cari NIS atau nama" wire:model.live.debounce.300ms="search">
        <table class="table"><thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
            @forelse($students as $item)
                <tr><td>{{ $item->student_number }}</td><td>{{ $item->full_name }}</td><td>{{ $item->classRoom?->name ?: '-' }}</td><td>{{ $item->status }}</td><td><button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">Ubah</button> <button class="btn btn-sm btn-outline-warning" wire:click="deactivate({{ $item->id }})">Nonaktifkan</button></td></tr>
            @empty
                <tr><td colspan="5" class="text-center">Belum ada siswa.</td></tr>
            @endforelse
        </tbody></table>
        {{ $students->links() }}
    </div>
</div>
