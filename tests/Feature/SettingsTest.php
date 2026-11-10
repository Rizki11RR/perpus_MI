<?php
namespace Tests\Feature;
use App\Models\LibrarySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Settings\LibrarySettings;
class SettingsTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_save_settings(): void
    {
        $admin=User::factory()->create(['role'=>'admin']);
        Livewire::actingAs($admin)->test(LibrarySettings::class)->set('maxBooksPerBorrower',5)->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('library_settings',['max_books_per_borrower'=>5,'updated_by'=>$admin->id]);
    }
    public function test_petugas_cannot_access_settings(): void
    {
        $this->actingAs(User::factory()->create(['role'=>'petugas']))->get('/admin/pengaturan')->assertForbidden();
    }
}
