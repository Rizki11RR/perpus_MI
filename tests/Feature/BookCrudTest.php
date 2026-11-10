<?php
namespace Tests\Feature;
use App\Livewire\Books\BookIndex;
use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
class BookCrudTest extends TestCase
{
    use RefreshDatabase;
    public function test_authenticated_user_can_create_book(): void
    {
        $category=BookCategory::create(['name'=>'Fiksi']);
        Livewire::actingAs(User::factory()->create())->test(BookIndex::class)->set('categoryId',$category->id)->set('bookCode','BK-001')->set('title','Buku Pertama')->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('books',['book_code'=>'BK-001','title'=>'Buku Pertama']);
    }
    public function test_book_cannot_be_deleted_when_copy_exists(): void
    {
        $category=BookCategory::create(['name'=>'Fiksi']); $book=$category->books()->create(['book_code'=>'BK-001','title'=>'Buku']); $book->copies()->create(['barcode'=>'BC-001']);
        Livewire::actingAs(User::factory()->create())->test(BookIndex::class)->call('delete',$book->id);
        $this->assertDatabaseHas('books',['id'=>$book->id]);
    }
}
