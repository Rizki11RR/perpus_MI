<?php
namespace Tests\Feature;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class DashboardTest extends TestCase
{
    use RefreshDatabase;
    public function test_dashboard_shows_real_catalog_counts(): void
    {
        $book=Book::factory()->create(); BookCopy::factory()->count(2)->create(['book_id'=>$book->id]);
        BookCopy::factory()->create(['book_id'=>$book->id,'status'=>'borrowed']);
        $response=$this->actingAs(User::factory()->create())->get('/');
        $response->assertOk()->assertSee('Judul buku')->assertSee('3');
    }
    public function test_empty_dashboard_is_honest(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertOk()->assertSee('Belum ada transaksi peminjaman.');
    }
}
