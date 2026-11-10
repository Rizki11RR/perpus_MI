<?php

namespace App\Livewire\Settings;

use App\Models\ActivityLog;
use App\Models\LibrarySetting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LibrarySettings extends Component
{
    public int $maxBooksPerBorrower = 3;
    public int $loanDurationDays = 7;
    public string $dailyFine = '0.00';
    public int $gracePeriodDays = 0;
    public string $timezone = 'Asia/Jakarta';

    public function mount(): void
    {
        $setting = LibrarySetting::query()->latest('id')->first();
        if (! $setting) return;
        $this->maxBooksPerBorrower = $setting->max_books_per_borrower;
        $this->loanDurationDays = $setting->loan_duration_days;
        $this->dailyFine = (string) $setting->daily_fine;
        $this->gracePeriodDays = $setting->grace_period_days;
        $this->timezone = $setting->timezone;
    }

    public function save(): void
    {
        $this->validate([
            'maxBooksPerBorrower' => ['required', 'integer', 'min:1', 'max:100'],
            'loanDurationDays' => ['required', 'integer', 'min:1', 'max:365'],
            'dailyFine' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'gracePeriodDays' => ['required', 'integer', 'min:0', 'max:365'],
            'timezone' => ['required', 'timezone'],
        ]);

        DB::transaction(function (): void {
            $setting = LibrarySetting::query()->latest('id')->first() ?? new LibrarySetting();
            $setting->fill([
                'max_books_per_borrower' => $this->maxBooksPerBorrower,
                'loan_duration_days' => $this->loanDurationDays,
                'daily_fine' => $this->dailyFine,
                'grace_period_days' => $this->gracePeriodDays,
                'timezone' => $this->timezone,
                'updated_by' => auth()->id(),
            ])->save();
            ActivityLog::create(['user_id' => auth()->id(), 'action' => 'updated', 'module' => 'settings', 'description' => 'Pengaturan sirkulasi diperbarui.']);
        });

        session()->flash('success', 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings.library-settings')->layout('components.layouts.app', ['heading' => 'Pengaturan']);
    }
}
