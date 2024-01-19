<?php

namespace App\Livewire\Admin\Roles;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleEdit extends Component
{

    public $roleId;
    public $role;

    public $name;
    public $guard_name;
    public $permissions;

    #[On('updateMultiSelect')]
    public function updateMultiSelect($values): void
    {
        $this->permissions = $values;
    }

    public function updateRole()
    {
        $this->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string',
        ]);

        $originalRole = Role::find($this->roleId);

        try {
            $this->role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            foreach ($this->role->permissions as $permission) {
                $this->role->revokePermissionTo($permission);
            }

            $this->role->syncPermissions($this->permissions);
        } catch (Exception $e) {
            Notification::make()
                ->title(__('messages.something_went_wrong'))
                ->danger()
                ->send();

            activity('system')
                ->performedOn($this->role)
                ->causedBy(auth()->user())
                ->withProperty('name', $this->role->name . ' (' . $this->role->guard_name . ')')
                ->withProperty('ip', request()->ip())
                ->log('role.update_failed');

            $this->dispatch('sendToConsole', $e->getMessage());
            return;
        }


        Notification::make()
            ->title(__('pages/admin/roles/messages.notifications.updated'))
            ->success()
            ->send();

        activity('system')
            ->performedOn($this->role)
            ->causedBy(auth()->user())
            ->withProperty('name', $this->role->name . ' (' . $this->role->guard_name . ')')
            ->withProperty('ip', request()->ip())
            ->withProperty('old', $originalRole->toJson())
            ->withProperty('new', $this->role->toJson())
            ->log('role.updated');

        return redirect()->route('admin-role-list');
    }

    public function mount()
    {
        $this->role = Role::find($this->roleId);
        if (!$this->role) {
            abort(404);
        }

        $this->name = $this->role->name;
        $this->guard_name = $this->role->guard_name;
    }

    public function render()
    {
        return view('livewire.admin.roles.role-edit')
            ->layout('components.layouts.admin', [
                'title' => __('navigation/titles.admin.roles.edit')
            ]);
    }
}