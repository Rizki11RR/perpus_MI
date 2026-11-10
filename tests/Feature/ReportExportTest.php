<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_catalog_csv_and_json(): void
    {
        Book::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user)->get('/laporan/books.csv')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->actingAs($user)->get('/laporan/books.json')->assertOk()->assertJsonCount(1);
    }
}
