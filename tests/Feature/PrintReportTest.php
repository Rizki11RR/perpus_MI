<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_print_pages(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/laporan/cetak/katalog')->assertOk()->assertSee('Katalog buku');
        $this->actingAs($user)->get('/laporan/cetak/transaksi')->assertOk()->assertSee('Laporan transaksi peminjaman');
    }
}
