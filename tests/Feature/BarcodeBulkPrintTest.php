<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarcodeBulkPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_print_selected_barcode_labels(): void
    {
        $copy = BookCopy::factory()->create();
        $this->actingAs(User::factory()->create())->get('/katalog/eksemplar/labels/'.$copy->id)->assertOk()->assertSee($copy->barcode);
    }
}
