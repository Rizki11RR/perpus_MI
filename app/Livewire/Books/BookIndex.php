<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithPagination;

class BookIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public ?int $editingId = null;
    public string $bookCode = '';
    public string $isbn = '';
    public string $title = '';
    public string $author = '';
    public string $publisher = '';
    public string $publicationYear = '';
    public string $description = '';
    public bool $isActive = true;
    public ?int $categoryId = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCategoryFilter(): void { $this->resetPage(); }
    public function edit(int $id): void
    {
        $book = Book::findOrFail($id);
        $this->editingId = $id;
        $this->bookCode = $book->book_code;
        $this->isbn = $book->isbn ?? '';
        $this->title = $book->title;
        $this->author = $book->author ?? '';
        $this->publisher = $book->publisher ?? '';
        $this->publicationYear = (string) ($book->publication_year ?? '');
        $this->description = $book->description ?? '';
        $this->isActive = $book->is_active;
        $this->categoryId = $book->category_id;
    }
    public function resetForm(): void { $this->reset(['editingId','bookCode','isbn','title','author','publisher','publicationYear','description','categoryId']); $this->isActive = true; }
    public function save(): void
    {
        $data = $this->validate([
            'categoryId' => ['required', 'integer', 'exists:book_categories,id'],
            'bookCode' => ['required', 'string', 'max:100', 'unique:books,book_code,'.$this->editingId],
            'isbn' => ['nullable', 'string', 'max:30'], 'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'], 'publisher' => ['nullable', 'string', 'max:255'],
            'publicationYear' => ['nullable', 'integer', 'min:1000', 'max:2100'], 'description' => ['nullable', 'string'],
            'isActive' => ['boolean'],
        ]);
        Book::updateOrCreate(['id' => $this->editingId], ['category_id'=>$data['categoryId'],'book_code'=>$data['bookCode'],'isbn'=>$data['isbn'],'title'=>$data['title'],'author'=>$data['author'],'publisher'=>$data['publisher'],'publication_year'=>$data['publicationYear'] !== '' ? $data['publicationYear'] : null,'description'=>$data['description'] !== '' ? $data['description'] : null,'is_active'=>$data['isActive']]);
        $this->resetForm(); session()->flash('success', 'Buku berhasil disimpan.');
    }
    public function deactivate(int $id): void { Book::findOrFail($id)->update(['is_active' => false]); session()->flash('success','Buku dinonaktifkan.'); }
    public function delete(int $id): void
    {
        $book = Book::findOrFail($id);
        if ($book->copies()->exists()) { session()->flash('error', 'Buku memiliki eksemplar dan tidak dapat dihapus.'); return; }
        $book->delete(); session()->flash('success', 'Buku berhasil dihapus.');
    }
    public function render()
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->when($this->search, function ($query): void {
                $query->where(function ($query): void {
                    $term = '%'.$this->search.'%';
                    $query->where('title', 'like', $term)
                        ->orWhere('book_code', 'like', $term)
                        ->orWhere('isbn', 'like', $term);
                });
            })
            ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.books.book-index', [
            'categories' => BookCategory::orderBy('name')->get(),
            'books' => $books,
        ])->layout('components.layouts.app', ['heading' => 'Katalog Buku']);
    }
}
