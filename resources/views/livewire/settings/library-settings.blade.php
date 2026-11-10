<div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <form wire:submit="save" class="card card-body">
        <h2 class="h5 mb-3">Pengaturan sirkulasi</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="maxBooksPerBorrower">Maksimal buku per peminjam</label><input id="maxBooksPerBorrower" type="number" min="1" max="100" class="form-control" wire:model="maxBooksPerBorrower">@error('maxBooksPerBorrower')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="loanDurationDays">Durasi pinjam (hari)</label><input id="loanDurationDays" type="number" min="1" max="365" class="form-control" wire:model="loanDurationDays">@error('loanDurationDays')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="dailyFine">Denda per hari (Rp)</label><input id="dailyFine" type="number" min="0" step="0.01" class="form-control" wire:model="dailyFine">@error('dailyFine')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="gracePeriodDays">Masa tenggang (hari)</label><input id="gracePeriodDays" type="number" min="0" max="365" class="form-control" wire:model="gracePeriodDays">@error('gracePeriodDays')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="timezone">Zona waktu</label><input id="timezone" class="form-control" wire:model="timezone">@error('timezone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        </div>
        <div class="mt-3"><button class="btn btn-primary" type="submit">Simpan pengaturan</button></div>
    </form>
</div>
