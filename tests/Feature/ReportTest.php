<?php

namespace Tests\Feature;

use App\Livewire\Reports\ReportIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_reports(): void
    {
        Livewire::actingAs(User::factory()->create())->test(ReportIndex::class)->assertSee('Transaksi peminjaman')->assertSee('Belum ada data.');
    }
}
