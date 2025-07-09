<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Audit Logs</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search all fields...">
                        </div>
                        <div class="col-md-2">
                            <select wire:model.live="filterByUser" class="form-control">
                                <option value="">Filter by User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select wire:model.live="filterByAction" class="form-control">
                                <option value="">Filter by Action Type</option>
                                @foreach($actionTypes as $actionType)
                                    <option value="{{ $actionType }}">{{ $actionType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" wire:model.live="filterByStartDate" class="form-control" placeholder="Start Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" wire:model.live="filterByEndDate" class="form-control" placeholder="End Date">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action Type</th>
                                    <th>Description</th>
                                    <th>Affected ID</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditLogs as $auditLog)
                                    <tr>
                                        <td>{{ $auditLog->user->name ?? 'N/A' }}</td>
                                        <td><span class="badge badge-info text-capitalize">{{ $auditLog->action }}</span></td>
                                        <td>{{ $auditLog->description }}</td>
                                        <td>{{ $auditLog->auditable_id ?? 'N/A' }}</td>
                                        <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No audit logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $auditLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>