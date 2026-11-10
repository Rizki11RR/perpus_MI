<?php

namespace App\Livewire\Backups;

use App\Models\BackupLog;
use App\Services\BackupService;
use Livewire\Component;
use Livewire\WithPagination;

class BackupIndex extends Component
{
    use WithPagination;
    public function create(BackupService $service): void { abort_unless(auth()->user()->isAdmin(), 403); $service->create(auth()->id()); session()->flash('success','Backup berhasil diverifikasi.'); }
    public function render() { return view('livewire.backups.backup-index',['backups'=>BackupLog::with('creator')->latest()->paginate(15)])->layout('components.layouts.app',['heading'=>'Backup']); }
}
