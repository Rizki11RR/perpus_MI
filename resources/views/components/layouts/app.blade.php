<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPUS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div class="d-flex">
    <aside class="sipus-sidebar d-none d-lg-block flex-shrink-0 p-3" style="width: 260px">
        <a href="{{ route('dashboard') }}" class="sipus-brand d-flex align-items-center gap-2 text-decoration-none fs-5 fw-semibold mb-4">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo SIPUS" width="32" height="32" class="rounded object-fit-cover"> SIPUS
        </a>
        <nav aria-label="Navigasi utama">
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid me-2"></i>Dashboard
            </a>
            <div class="text-muted small text-uppercase px-3 mt-3 mb-1">Katalog</div>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('catalog.categories') ? 'active' : '' }}" href="{{ route('catalog.categories') }}">Kategori buku</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('catalog.books') ? 'active' : '' }}" href="{{ route('catalog.books') }}">Buku</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('catalog.copies') ? 'active' : '' }}" href="{{ route('catalog.copies') }}">Eksemplar</a>
            <div class="text-muted small text-uppercase px-3 mt-3 mb-1">Data</div>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('data.classes') ? 'active' : '' }}" href="{{ route('data.classes') }}">Kelas</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('data.students') ? 'active' : '' }}" href="{{ route('data.students') }}">Siswa</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('data.teachers') ? 'active' : '' }}" href="{{ route('data.teachers') }}">Guru</a>
            @if(auth()->user()->isAdmin())
                <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('data.staff') ? 'active' : '' }}" href="{{ route('data.staff') }}">Petugas</a>
            @endif
            <div class="text-muted small text-uppercase px-3 mt-3 mb-1">Operasional</div>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('circulation') ? 'active' : '' }}" href="{{ route('circulation') }}">Sirkulasi</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('reports*') ? 'active' : '' }}" href="{{ route('reports') }}">Laporan</a>
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('imports') ? 'active' : '' }}" href="{{ route('imports') }}">Import data</a>
            @if(auth()->user()->isAdmin())
                <div class="text-muted small text-uppercase px-3 mt-3 mb-1">Admin</div>
                <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('backups*') ? 'active' : '' }}" href="{{ route('backups') }}">Backup</a>
                <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Profil perpustakaan</a>
                <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Pengaturan</a>
            @endif
        </nav>
    </aside>
    <main class="sipus-main flex-grow-1">
        <header class="sipus-topbar d-flex justify-content-between align-items-center px-3 px-lg-4 py-3">
            <div>
                <span class="text-muted small">Sistem Informasi Perpustakaan Sekolah</span>
                <h1 class="h5 mb-0">{{ $heading ?? 'Dashboard' }}</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Keluar</button>
                </form>
            </div>
        </header>
        <section class="p-3 p-lg-4">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive">
                    <strong>Periksa data berikut:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            <div id="livewire-error" class="alert alert-danger alert-dismissible fade show d-none" role="alert" aria-live="assertive">
                <span></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
            <div wire:loading class="alert alert-info" role="status" aria-live="polite">Memproses permintaan...</div>
            {{ $slot }}
        </section>
    </main>
</div>
@livewireScripts
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                const alert = document.getElementById('livewire-error');
                if (alert) {
                    alert.querySelector('span').textContent = `Permintaan gagal (${status}). Periksa data lalu coba lagi.`;
                    alert.classList.remove('d-none');
                }
                preventDefault();
            });
        });
    });
</script>
</body>
</html>
