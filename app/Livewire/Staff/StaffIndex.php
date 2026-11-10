<?php

namespace App\Livewire\Staff;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class StaffIndex extends Component
{
    use WithPagination;

    public string $search = '', $staffNumber = '', $fullName = '', $position = '', $phone = '', $email = '', $username = '', $password = '';
    public bool $isActive = true;
    public ?int $editingId = null;

    public function updatedSearch(): void { $this->resetPage(); }

    public function edit(int $id): void
    {
        $x = Staff::with('user')->findOrFail($id);
        $this->editingId = $id;
        $this->staffNumber = $x->staff_number; $this->fullName = $x->full_name;
        $this->position = $x->position ?? ''; $this->phone = $x->phone ?? '';
        $this->email = $x->user?->email ?? ''; $this->username = $x->user?->username ?? ''; $this->password = '';
        $this->isActive = $x->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'staffNumber', 'fullName', 'position', 'phone', 'email', 'username', 'password']);
        $this->isActive = true;
    }

    public function deactivate(int $id): void
    {
        $staff = Staff::with('user')->findOrFail($id);
        DB::transaction(function () use ($staff): void {
            $staff->update(['is_active' => false]);
            $staff->user?->update(['is_active' => false]);
        });
        session()->flash('success', 'Petugas dinonaktifkan.');
    }

    public function save(): void
    {
        $data = $this->validate([
            'staffNumber' => ['required', 'string', 'max:100', Rule::unique('staff', 'staff_number')->ignore($this->editingId)],
            'fullName' => ['required', 'string', 'max:255'], 'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'], 'isActive' => ['boolean'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Staff::find($this->editingId)?->user_id)],
            'username' => ['required', 'string', 'alpha_dash', 'max:100', Rule::unique('users', 'username')->ignore(Staff::find($this->editingId)?->user_id)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ]);
        DB::transaction(function () use ($data): void {
            $staff = Staff::updateOrCreate(['id' => $this->editingId], [
                'staff_number' => $data['staffNumber'], 'full_name' => $data['fullName'], 'position' => $data['position'],
                'phone' => $data['phone'], 'is_active' => $data['isActive'],
            ]);
            $user = $staff->user ?: new User(['role' => 'petugas']);
            $user->fill(['name' => $data['fullName'], 'email' => $data['email'], 'username' => $data['username'], 'role' => 'petugas', 'is_active' => $data['isActive']]);
            if ($data['password'] ?? null) $user->password = $data['password'];
            $user->save();
            if ($staff->user_id !== $user->id) $staff->update(['user_id' => $user->id]);
        });
        $this->resetForm(); session()->flash('success', 'Data petugas berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.staff.staff-index', ['staff' => Staff::with('user')->when($this->search, fn ($q) => $q->where(fn ($q) => $q->where('staff_number', 'like', '%'.$this->search.'%')->orWhere('full_name', 'like', '%'.$this->search.'%')))->latest()->paginate(10)])->layout('components.layouts.app', ['heading' => 'Data Petugas']);
    }
}
