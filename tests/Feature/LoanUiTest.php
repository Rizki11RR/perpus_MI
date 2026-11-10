<?php

namespace Tests\Feature;

use App\Livewire\Circulation\LoanIndex;
use App\Models\BookCopy;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoanUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_copy_tag_can_be_removed(): void
    {
        $first = BookCopy::factory()->create();
        $second = BookCopy::factory()->create();
        Livewire::actingAs(User::factory()->create())->test(LoanIndex::class)
            ->set('copyIds', [$first->id, $second->id])
            ->call('removeCopy', $first->id)
            ->assertSet('copyIds', [$second->id]);
    }

    public function test_borrower_search_filters_students_and_teachers(): void
    {
        $student = Student::factory()->create(['student_number'=>'S-ALFA','full_name'=>'Alfa']);
        Student::factory()->create(['student_number'=>'S-BETA','full_name'=>'Beta']);
        $teacher = Teacher::factory()->create(['teacher_number'=>'G-ALFA','full_name'=>'Alfa Guru']);
        Teacher::factory()->create(['teacher_number'=>'G-BETA','full_name'=>'Beta Guru']);
        Livewire::actingAs(User::factory()->create())->test(LoanIndex::class)
            ->set('borrowerSearch', 'ALFA')->assertSee('S-ALFA')->assertDontSee('S-BETA')
            ->set('teacherSearch', 'ALFA')->assertSee('G-ALFA')->assertDontSee('G-BETA');
    }
}
