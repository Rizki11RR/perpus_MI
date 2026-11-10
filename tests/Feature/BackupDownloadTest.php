<?php

namespace Tests\Feature;

use App\Models\BackupLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_download_verified_intact_backup(): void
    {
        Storage::fake('local'); $admin = User::factory()->create(['role'=>'admin']); $path='backups/test.json'; $content='{"ok":true}'; Storage::disk('local')->put($path,$content);
        $backup=BackupLog::create(['created_by'=>$admin->id,'file_name'=>'test.json','path'=>$path,'file_size'=>strlen($content),'type'=>'json','status'=>'verified']);
        $this->actingAs($admin)->get(route('backups.download',$backup))->assertOk();
        $this->actingAs(User::factory()->create(['role'=>'petugas']))->get(route('backups.download',$backup))->assertForbidden();
    }
}
