<?php

namespace App\Livewire\Members;

use App\Models\LibraryClass;
use Livewire\Component;
use Livewire\WithPagination;

class ClassIndex extends Component
{
    use WithPagination;
    public string $search = '', $name = '';
    public ?int $editingId = null;
    public function updatedSearch(): void { $this->resetPage(); }
    public function edit(int $id): void { $class = LibraryClass::findOrFail($id); $this->editingId = $id; $this->name = $class->name; }
    public function resetForm(): void { $this->reset(['editingId', 'name']); }
    public function save(): void { $data = $this->validate(['name' => ['required', 'string', 'max:100', 'unique:classes,name,'.$this->editingId]]); LibraryClass::updateOrCreate(['id' => $this->editingId], ['name' => $data['name']]); $this->resetForm(); session()->flash('success', 'Kelas berhasil disimpan.'); }
    public function render() { return view('livewire.members.class-index', ['classes' => LibraryClass::when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(10)])->layout('components.layouts.app', ['heading' => 'Kelas']); }
}
