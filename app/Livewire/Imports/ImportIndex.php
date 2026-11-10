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
    public function downloadTemplate(): mixed { $headers = match ($this->type) { 'books'=>['book_code','title','category','author','publisher'], 'students'=>['student_number','full_name','class'], default=>['teacher_number','full_name'] }; return response()->streamDownload(function () use ($headers) { $handle = fopen('php://output', 'w'); fputcsv($handle, $headers); fclose($handle); }, $this->type.'-template.csv', ['Content-Type'=>'text/csv']); }

    public function preview(ImportService $service): void
    {
        $this->validate(['type'=>'required|in:books,students,teachers','file'=>'required|file|mimes:csv,txt|max:2048']);
        $this->batchId = $service->preview($this->file, $this->type, auth()->id())->id;
        session()->flash('success', 'Preview import berhasil dibuat.');
    }

    public function cancelBatch(): void { if ($this->batchId) ImportBatch::whereKey($this->batchId)->where('uploaded_by', auth()->id())->delete(); $this->reset(['file','batchId']); session()->flash('success','Preview dibatalkan.'); }

    public function commit(ImportService $service): void
    {
        $this->validate(['batchId'=>'required|integer']);
        $batch = ImportBatch::with('rows')->whereKey($this->batchId)->where('uploaded_by', auth()->id())->first();
        if (! $batch) { $this->addError('batch', 'Preview import tidak ditemukan.'); return; }
        if ($batch->status !== 'preview') { $this->addError('batch', 'Preview import sudah diproses.'); return; }
        try { $service->commit($batch, auth()->id(), true); }
        catch (\Throwable $exception) { report($exception); $this->addError('batch', 'Import gagal disimpan. Coba lagi.'); return; }
        $this->reset(['file', 'batchId']);
        session()->flash('success', 'Import berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.imports.import-index', ['batch'=>$this->batchId ? ImportBatch::with('rows')->find($this->batchId) : null])->layout('components.layouts.app', ['heading'=>'Import data']);
    }
}
