<?php

namespace App\Livewire\Imports;

use App\Models\ImportBatch;
use App\Services\ImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportIndex extends Component
{
    use WithFileUploads;

    public string $type = 'books';
    public $file;
    public ?int $batchId = null;
    public string $duplicateMode = 'skip';
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public int $failedCount = 0;

    public function mount(?string $type = null): void
    {
        if ($type) $this->type = $type;
    }

    public function downloadTemplate(): mixed
    {
        $rows = match ($this->type) {
            'books' => [['Kode Buku', 'Judul', 'Kategori', 'ISBN', 'Penulis', 'Penerbit'], ['BK-001', 'Contoh Buku', 'Fiksi', '9780000000000', 'Nama Penulis', 'Penerbit']],
            'students' => [['NIS', 'Nama Lengkap', 'Kelas'], ['S-001', 'Contoh Siswa', '1A']],
            default => [['NIP', 'Nama Lengkap', 'Jabatan'], ['G-001', 'Contoh Guru', 'Guru']],
        };
        return response()->streamDownload(function () use ($rows): void { $handle = fopen('php://output', 'w'); foreach ($rows as $row) fputcsv($handle, $row); fclose($handle); }, $this->type.'-template.csv', ['Content-Type'=>'text/csv']);
    }

    public function preview(ImportService $service): void
    {
        $this->validate(['type'=>'required|in:books,students,teachers','duplicateMode'=>'required|in:skip,update','file'=>'required|file|mimes:csv,txt,xlsx|max:10240']);
        $batch = $service->preview($this->file, $this->type, auth()->id(), $this->duplicateMode);
        $this->batchId = $batch->id;
        session()->flash('success', 'Preview import berhasil dibuat.');
    }

    public function cancelBatch(): void { if ($this->batchId) ImportBatch::whereKey($this->batchId)->where('uploaded_by', auth()->id())->delete(); $this->reset(['file','batchId']); session()->flash('success','Preview dibatalkan.'); }

    public function commit(ImportService $service): void
    {
        $this->validate(['batchId'=>'required|integer']);
        $batch = ImportBatch::with('rows')->whereKey($this->batchId)->where('uploaded_by', auth()->id())->first();
        if (! $batch) { $this->addError('batch', 'Preview import tidak ditemukan.'); return; }
        if ($batch->status !== 'preview') { $this->addError('batch', 'Preview import sudah diproses.'); return; }
        try {
            $this->importedCount = (int) $batch->valid_rows;
            $this->skippedCount = (int) $batch->invalid_rows;
            $service->commit($batch, auth()->id(), true);
        } catch (\Throwable $exception) { report($exception); $this->failedCount++; $this->addError('batch', 'Import gagal disimpan. Coba lagi.'); return; }
        $this->reset(['file', 'batchId']);
        session()->flash('success', "Import selesai. Berhasil: {$this->importedCount}. Dilewati: {$this->skippedCount}. Gagal: {$this->failedCount}.");
    }

    public function render()
    {
        return view('livewire.imports.import-index', ['batch'=>$this->batchId ? ImportBatch::with('rows')->find($this->batchId) : null])->layout('components.layouts.app', ['heading'=>'Import data']);
    }
}
