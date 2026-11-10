<?php
namespace Tests\Feature;
use App\Livewire\Books\CopyIndex;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
class CopyCrudTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_create_copy_with_unique_barcode(): void
    {
        $book=Book::create(['category_id'=>BookCategory::create(['name'=>'Fiksi'])->id,'book_code'=>'BK-001','title'=>'Buku']);
        Livewire::actingAs(User::factory()->create())->test(CopyIndex::class)->set('bookId',$book->id)->set('barcode','BC-001')->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('book_copies',['barcode'=>'BC-001']);
    }
    public function test_copy_with_history_cannot_be_deleted(): void
    {
        $copy=BookCopy::factory()->create(); $copy->loanItems()->create(['loan_id'=>\App\Models\Loan::factory()->create()->id,'borrowed_at'=>now(),'due_date'=>now()->addDays(7),'status'=>'borrowed']);
        Livewire::actingAs(User::factory()->create())->test(CopyIndex::class)->call('delete',$copy->id);
        $this->assertDatabaseHas('book_copies',['id'=>$copy->id]);
    }
}
