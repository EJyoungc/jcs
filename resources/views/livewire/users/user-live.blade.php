<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>
                    <div class="card-tools">
                        <button wire:click="create()" class="btn btn-primary btn-sm">
                            Add User <x-spinner for="create" />
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search by name or email...">
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterByRole" class="form-control">
                                <option value="">Filter by Role</option>
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption }}">{{ ucfirst(str_replace('_', ' ', $roleOption)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterByStatus" class="form-control">
                                <option value="">Filter by Status</option>
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge badge-info text-capitalize">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span></td>
                                        <td>
                                            @if ($user->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="create({{ $user->id }})" class="btn btn-sm btn-warning">
                                                Edit
                                            </button>
                                            @if ($user->status == 'active')
                                                <button wire:click="deactivateUser({{ $user->id }})" class="btn btn-sm btn-secondary">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button wire:click="activateUser({{ $user->id }})" class="btn btn-sm btn-success">
                                                    Activate
                                                </button>
                                            @endif
                                            <button wire:click="resetPassword({{ $user->id }})" class="btn btn-sm btn-info">
                                                Reset Password
                                            </button>
                                            <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal title="{{ $selectedUser ? 'Edit User: ' . $selectedUser->name : 'Add User' }}" :status="$modal">
        <form wire:submit.prevent="store">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" wire:model='name'>
                <x-error for="name" />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" wire:model='email'>
                <x-error for="email" />
            </div>
            @if (!$selectedUser)
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" wire:model='password'>
                    <x-error for="password" />
                </div>
            @endif
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" wire:model='role'>
                    <option value="">Select Role</option>
                    @foreach($roles as $roleOption)
                        <option value="{{ $roleOption }}">{{ ucfirst(str_replace('_', ' ', $roleOption)) }}</option>
                    @endforeach
                </select>
                <x-error for="role" />
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" wire:model='status'>
                    <option value="">Select Status</option>
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
                <x-error for="status" />
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">
                    Save <x-spinner for="store" />
                </button>
            </div>
        </form>
    </x-modal>
</div>