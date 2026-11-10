<div>
 <div class="card"><div class="card-body"><h2 class="h5 mb-4">Profil sekolah dan perpustakaan</h2>
 @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
 <div class="mb-4"><img src="{{ asset($logoPath) }}" alt="Logo perpustakaan" style="max-height:80px;max-width:240px"></div>
 <p class="small text-muted">Logo menggunakan docs/logo.jpeg sebagai asset proyek.</p>
 <form wire:submit="save"><div class="mb-3"><label class="form-label" for="schoolName">Nama sekolah</label><input id="schoolName" class="form-control @error('schoolName') is-invalid @enderror" wire:model="schoolName" required>@error('schoolName')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-3"><label class="form-label" for="libraryName">Nama perpustakaan</label><input id="libraryName" class="form-control" wire:model="libraryName"></div><div class="mb-3"><label class="form-label" for="address">Alamat</label><textarea id="address" class="form-control" wire:model="address"></textarea></div><div class="row g-3"><div class="col-md-6"><label class="form-label" for="phone">Telepon</label><input id="phone" class="form-control" wire:model="phone"></div><div class="col-md-6"><label class="form-label" for="profileEmail">Email</label><input id="profileEmail" type="email" class="form-control" wire:model="email"></div></div><button class="btn btn-primary mt-4" type="submit" wire:loading.attr="disabled">Simpan profil</button></form>
 </div></div>
</div>
