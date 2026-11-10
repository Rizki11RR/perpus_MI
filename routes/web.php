<?php
use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
Route::middleware('guest')->group(function () { Route::get('/login', [AuthController::class, 'create'])->name('login'); Route::post('/login', [AuthController::class, 'store'])->name('login.store'); });
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/admin-only', fn () => 'admin')->middleware('role:admin')->name('admin.only');
});

use App\Livewire\Settings\LibraryProfile;
use App\Livewire\Settings\LibrarySettings;

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profil', LibraryProfile::class)->name('profile');
    Route::get('/pengaturan', LibrarySettings::class)->name('settings');
});

use App\Livewire\Books\CategoryIndex;
use App\Livewire\Books\BookIndex;
use App\Livewire\Books\CopyIndex;
Route::middleware(['auth', 'active'])->prefix('katalog')->name('catalog.')->group(function () {
    Route::get('/kategori', CategoryIndex::class)->name('categories');
    Route::get('/buku', BookIndex::class)->name('books');
    Route::get('/eksemplar', CopyIndex::class)->name('copies');
});

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\PrintController;
Route::middleware(['auth', 'active'])->prefix('katalog/eksemplar')->name('catalog.copy.')->group(function () {
 Route::get('/{copy}/barcode.png', [BarcodeController::class, 'image'])->name('barcode.image');
 Route::get('/{copy}/label', [BarcodeController::class, 'print'])->name('label');
  Route::get('/labels/{ids}', [BarcodeController::class, 'labels'])->name('labels');
});

use App\Livewire\Members\ClassIndex;
use App\Livewire\Members\StudentIndex;
use App\Livewire\Members\TeacherIndex;
use App\Livewire\Staff\StaffIndex;
use App\Livewire\Circulation\LoanIndex;
use App\Livewire\Reports\ReportIndex;
use App\Livewire\Imports\ImportIndex;
use App\Livewire\Backups\BackupIndex;
use App\Http\Controllers\BackupController;
Route::middleware(['auth', 'active'])->prefix('data')->name('data.')->group(function () {
 Route::get('/kelas', ClassIndex::class)->name('classes'); Route::get('/siswa', StudentIndex::class)->name('students'); Route::get('/guru', TeacherIndex::class)->name('teachers'); Route::get('/petugas', StaffIndex::class)->name('staff');
});
Route::middleware(['auth', 'active'])->group(function () {
 Route::get('/sirkulasi', LoanIndex::class)->name('circulation');
 Route::get('/laporan', ReportIndex::class)->name('reports');
 Route::get('/import', ImportIndex::class)->name('imports');
 Route::get('/backup', BackupIndex::class)->middleware('role:admin')->name('backups');
 Route::get('/backup/{backup}/download', [BackupController::class, 'download'])->middleware('role:admin')->name('backups.download');
 Route::get('/laporan/{type}.csv', [ReportExportController::class, 'csv'])->whereIn('type', ['loans', 'books', 'students', 'teachers'])->name('reports.csv');
 Route::get('/laporan/{type}.json', [ReportExportController::class, 'json'])->whereIn('type', ['loans', 'books', 'students', 'teachers'])->name('reports.json');
 Route::get('/laporan/cetak/katalog', [PrintController::class, 'catalog'])->name('reports.print.catalog');
 Route::get('/laporan/cetak/transaksi', [PrintController::class, 'loans'])->name('reports.print.loans');
});
