<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\User;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ImportOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_commit_another_users_batch(): void
    {
        $owner = User::factory()->create(); $other = User::factory()->create();
        $batch = app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('students.csv', "student_number,full_name, class\nS-1,Satu,\n"), 'students', $owner->id);
        $this->expectException(ValidationException::class); app(ImportService::class)->commit($batch, $other->id);
    }
}
