<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\BookCopy;
use Livewire\Component;
use Livewire\WithPagination;

class CopyIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $bookFilter = '';
    public ?int $editingId = null;
    public array $selectedIds = [];
    public ?int $bookId = null;
    public string $barcode = '';
    public string $shelfLocation = '';
    public string $condition = 'good';
    public string $status = 'available';

    public function updatedSearch(): void { $this->resetPage(); }
    public function printSelected(): mixed { $this->validate(['selectedIds'=>'array','selectedIds.*'=>'integer|exists:book_copies,id']); abort_unless(count($this->selectedIds) > 0, 422); return redirect()->route('catalog.copy.labels', ['ids'=>implode(',', $this->selectedIds)]); }
    public function updatedBookFilter(): void { $this->resetPage(); }
    public function edit(int $id): void { $copy=BookCopy::findOrFail($id); $this->editingId=$id; $this->bookId=$copy->book_id; $this->barcode=$copy->barcode; $this->shelfLocation=$copy->shelf_location??''; $this->condition=$copy->condition; $this->status=$copy->status; }
    public function resetForm(): void { $this->reset(['editingId','bookId','barcode','shelfLocation']); $this->condition='good'; $this->status='available'; }
    public function save(): void
    {
        $data=$this->validate(['bookId'=>['required','integer','exists:books,id'],'barcode'=>['required','string','max:100','unique:book_copies,barcode,'.$this->editingId],'shelfLocation'=>['nullable','string','max:100'],'condition'=>['required','in:good,minor_damage,major_damage'],'status'=>['required','in:available,borrowed,damaged,lost']]);
        if ($this->editingId && in_array($this->status, ['borrowed'], true)) { $this->addError('status','Status dipinjam hanya berubah melalui transaksi.'); return; }
        BookCopy::updateOrCreate(['id'=>$this->editingId],['book_id'=>$data['bookId'],'barcode'=>$data['barcode'],'shelf_location'=>$data['shelfLocation'],'condition'=>$data['condition'],'status'=>$data['status']]); $this->resetForm(); session()->flash('success','Eksemplar berhasil disimpan.');
    }
    public function delete(int $id): void { $copy=BookCopy::findOrFail($id); if($copy->loanItems()->exists()){session()->flash('error','Eksemplar memiliki histori transaksi dan tidak dapat dihapus.');return;} $copy->delete(); session()->flash('success','Eksemplar berhasil dihapus.'); }
    public function render()
    {
        $copies=BookCopy::with('book')->when($this->search,fn($q)=>$q->where('barcode','like','%'.$this->search.'%')->orWhereHas('book',fn($q)=>$q->where('title','like','%'.$this->search.'%')))->when($this->bookFilter,fn($q)=>$q->where('book_id',$this->bookFilter))->latest()->paginate(10);
        return view('livewire.books.copy-index',['books'=>Book::orderBy('title')->get(),'copies'=>$copies])->layout('components.layouts.app',['heading'=>'Eksemplar Buku']);
    }
}
