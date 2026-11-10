<?php

namespace Tests\Feature;

use App\Livewire\Books\BookIndex;
use App\Livewire\Members\StudentIndex;
use App\Livewire\Members\TeacherIndex;
use App\Livewire\Staff\StaffIndex;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DeactivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_referenced_master_records_can_be_deactivated_and_hidden(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $student = Student::factory()->create();
        $teacher = Teacher::factory()->create();
        $staff = Staff::factory()->create();

        Livewire::actingAs($user)->test(BookIndex::class)->call('deactivate', $book->id);
        Livewire::actingAs($user)->test(StudentIndex::class)->call('deactivate', $student->id);
        Livewire::actingAs($user)->test(TeacherIndex::class)->call('deactivate', $teacher->id);
        Livewire::actingAs($user)->test(StaffIndex::class)->call('deactivate', $staff->id);

        $this->assertFalse($book->fresh()->is_active);
        $this->assertFalse($student->fresh()->is_active);
        $this->assertFalse($teacher->fresh()->is_active);
        $this->assertFalse($staff->fresh()->is_active);
    }
}
