<?php

namespace App\Livewire\Books;

use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $description = '';
    public ?int $editingId = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function edit(int $id): void { $category=BookCategory::findOrFail($id); $this->editingId=$id; $this->name=$category->name; $this->description=$category->description ?? ''; }
    public function resetForm(): void { $this->reset(['name','description','editingId']); }
    public function save(): void
    {
        $data=$this->validate(['name'=>['required','string','max:255','unique:book_categories,name,'.$this->editingId],'description'=>['nullable','string']]);
        BookCategory::updateOrCreate(['id'=>$this->editingId],$data); $this->resetForm(); session()->flash('success','Kategori berhasil disimpan.');
    }
    public function deactivate(int $id): void { BookCategory::findOrFail($id)->update(['is_active'=>false]); session()->flash('success','Kategori dinonaktifkan.'); }
    public function delete(int $id): void { $category=BookCategory::findOrFail($id); if($category->books()->exists()){session()->flash('error','Kategori memiliki buku dan tidak dapat dihapus.');return;} $category->delete(); session()->flash('success','Kategori berhasil dihapus.'); }
    public function render(){return view('livewire.books.category-index',['categories'=>BookCategory::query()->where('is_active',true)->when($this->search,fn($q)=>$q->where('name','like','%'.$this->search.'%'))->latest()->paginate(10)])->layout('components.layouts.app',['heading'=>'Kategori Buku']);}
}
