<?php

namespace Tests\Feature;

use App\Models\BackupLog;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_verified_private_backup(): void
    {
        Storage::fake('local'); $admin = User::factory()->create(['role'=>'admin']);
        $log = app(BackupService::class)->create($admin->id);
        Storage::disk('local')->assertExists($log->path); $this->assertSame('verified', $log->status);
    }

    public function test_petugas_cannot_open_backup_page(): void
    {
        $this->actingAs(User::factory()->create(['role'=>'petugas']))->get('/backup')->assertForbidden();
    }
}
