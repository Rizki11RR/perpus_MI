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
            <i class="bi bi-book"></i> SIPUS
        </a>
        <nav aria-label="Navigasi utama">
            <a class="nav-link px-3 py-2 mb-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid me-2"></i>Dashboard
            </a>
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
            {{ $slot }}
        </section>
    </main>
</div>
@livewireScripts
</body>
</html>
