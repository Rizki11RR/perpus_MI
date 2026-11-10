<?php

namespace App\Livewire\Settings;

use App\Models\LibraryProfile as Profile;
use Livewire\Component;

class LibraryProfile extends Component
{
    public string $schoolName = '';
    public string $libraryName = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $logoPath = 'images/logo.jpeg';

    public function mount(): void
    {
        $profile = Profile::query()->first();
        if ($profile) {
            $this->schoolName = $profile->school_name;
            $this->libraryName = $profile->library_name ?? '';
            $this->address = $profile->address ?? '';
            $this->phone = $profile->phone ?? '';
            $this->email = $profile->email ?? '';
            $this->logoPath = $profile->logo_path ?: 'images/logo.jpeg';
        }
    }

    public function save(): void
    {
        $this->validate([
            'schoolName' => ['required', 'string', 'max:255'],
            'libraryName' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Profile::updateOrCreate(['id' => 1], [
            'school_name' => $this->schoolName,
            'library_name' => $this->libraryName,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_path' => $this->logoPath,
        ]);

        session()->flash('success', 'Profil berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings.library-profile')->layout('components.layouts.app', ['heading' => 'Profil Perpustakaan']);
    }
}
