<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserLive extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $modal = false;
    public $selectedUser;

    // form fields
    public $name, $email, $password, $role, $status;

    // filters and search
    public $search = '';
    public $filterByRole = '';
    public $filterByStatus = '';

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
        'filterByRole' => ['except' => '', 'as' => 'role'],
        'filterByStatus' => ['except' => '', 'as' => 'status'],
    ];

    public function boot()
    {
        // Access control is now handled by middleware
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterByRole()
    {
        $this->resetPage();
    }

    public function updatingFilterByStatus()
    {
        $this->resetPage();
    }

    public function create($id = null)
    {
        if (empty($id)) {
            $this->reset(['selectedUser', 'name', 'email', 'password', 'role', 'status']);
            $this->modal = true;
        } else {
            $this->selectedUser = User::findOrFail($id);
            $this->name = $this->selectedUser->name;
            $this->email = $this->selectedUser->email;
            $this->role = $this->selectedUser->role;
            $this->status = $this->selectedUser->status;
            $this->password = null; // Password should not be pre-filled for security
            $this->modal = true;
        }
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($this->selectedUser ? $this->selectedUser->id : 'null'),
            'role' => 'required|string|in:system_administrator,chairperson,jtc_member,training_officer,district_court_clerk,candidate',
            'status' => 'required|string|in:active,inactive',
        ];

        if (empty($this->selectedUser)) {
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        if (empty($this->selectedUser)) {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'status' => $this->status,
            ]);
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'User Created',
                'description' => 'User ' . $user->name . ' (ID: ' . $user->id . ') created.',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
            ]);
            $this->alert('success', 'User created successfully.');
        } else {
            $this->selectedUser->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
            ]);
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'User Updated',
                'description' => 'User ' . $this->selectedUser->name . ' (ID: ' . $this->selectedUser->id . ') updated.',
                'auditable_type' => User::class,
                'auditable_id' => $this->selectedUser->id,
            ]);
            $this->alert('success', 'User updated successfully.');
        }

        $this->cancel();
    }

    public function activateUser(User $user)
    {
        $user->update(['status' => 'active']);
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'User Activated',
            'description' => 'User ' . $user->name . ' (ID: ' . $user->id . ') activated.',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
        $this->alert('success', 'User activated successfully.');
    }

    public function deactivateUser(User $user)
    {
        $user->update(['status' => 'inactive']);
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'User Deactivated',
            'description' => 'User ' . $user->name . ' (ID: ' . $user->id . ') deactivated.',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
        $this->alert('success', 'User deactivated successfully.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('root')]); // Default password
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'User Password Reset',
            'description' => 'Password for user ' . $user->name . ' (ID: ' . $user->id . ') reset to default.',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
        $this->alert('success', 'User password reset to default.');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $userId = $user->id;
        $user->delete();
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'User Deleted',
            'description' => 'User ' . $userName . ' (ID: ' . $userId . ') deleted.',
            'auditable_type' => User::class,
            'auditable_id' => $userId,
        ]);
        $this->alert('success', 'User deleted successfully.');
    }

    public function confirmDelete($id)
    {
        $this->confirm('Are you sure you want to delete this user?', [
            'onConfirmed' => 'delete',
            'params' => $id,
        ]);
    }

    public function cancel()
    {
        $this->reset(["selectedUser", "name", "email", "password", "role", "status", "modal"]);
        $this->dispatch('modal-cancel');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterByRole, function ($query) {
                $query->where('role', $this->filterByRole);
            })
            ->when($this->filterByStatus, function ($query) {
                $query->where('status', $this->filterByStatus);
            })
            ->paginate(10);

        $roles = ['system_administrator', 'chairperson', 'jtc_member', 'training_officer', 'district_court_clerk', 'candidate'];
        $statuses = ['active', 'inactive'];

        return view('livewire.users.user-live', [
            'users' => $users,
            'roles' => $roles,
            'statuses' => $statuses,
        ]);
    }
}
