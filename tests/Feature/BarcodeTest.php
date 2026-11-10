<?php
namespace Tests\Feature;
use App\Models\BookCopy;
use App\Models\User;
use App\Services\BarcodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class BarcodeTest extends TestCase
{
    use RefreshDatabase;
    public function test_barcode_service_generates_png(): void
    {
        $png=(new BarcodeService())->png('BC-001');
        $this->assertStringStartsWith("\x89PNG",$png);
    }
    public function test_authenticated_user_can_view_barcode_image(): void
    {
        $copy=BookCopy::factory()->create();
        $this->actingAs(User::factory()->create())->get(route('catalog.copy.barcode.image',$copy))->assertOk()->assertHeader('Content-Type','image/png');
    }
}
