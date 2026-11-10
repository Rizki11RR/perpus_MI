<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_protected_operational_routes(): void
    {
        foreach (['/katalog/buku','/data/siswa','/sirkulasi','/laporan','/import','/backup'] as $path) $this->get($path)->assertRedirect('/login');
    }

    public function test_petugas_cannot_open_admin_or_backup_routes(): void
    {
        $user = User::factory()->create(['role'=>'petugas']);
        $this->actingAs($user)->get('/admin/pengaturan')->assertForbidden();
        $this->actingAs($user)->get('/backup')->assertForbidden();
    }
}
