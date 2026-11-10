<?php

namespace Tests\Feature;

use App\Livewire\Books\BookIndex;
use App\Livewire\Books\CopyIndex;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_search_filters_title_and_code(): void
    {
        $category = BookCategory::create(['name' => 'Fiksi']);
        Book::create(['category_id' => $category->id, 'book_code' => 'BK-ALFA', 'title' => 'Alfa']);
        Book::create(['category_id' => $category->id, 'book_code' => 'BK-BETA', 'title' => 'Beta']);

        Livewire::actingAs(User::factory()->create())->test(BookIndex::class)
            ->set('search', 'Alfa')
            ->assertSee('BK-ALFA')
            ->assertDontSee('BK-BETA');
    }

    public function test_copy_filter_filters_by_book(): void
    {
        $category = BookCategory::create(['name' => 'Fiksi']);
        $first = Book::create(['category_id' => $category->id, 'book_code' => 'BK-ALFA', 'title' => 'Alfa']);
        $second = Book::create(['category_id' => $category->id, 'book_code' => 'BK-BETA', 'title' => 'Beta']);
        BookCopy::create(['book_id' => $first->id, 'barcode' => 'BC-ALFA']);
        BookCopy::create(['book_id' => $second->id, 'barcode' => 'BC-BETA']);

        Livewire::actingAs(User::factory()->create())->test(CopyIndex::class)
            ->set('bookFilter', $first->id)
            ->assertSee('BC-ALFA')
            ->assertDontSee('BC-BETA');
    }
}
