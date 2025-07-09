<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Application Queue</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Application Queue</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Applications</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control float-right" placeholder="Search by ID or User Name">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="statusFilter" class="col-sm-2 col-form-label">Filter by Status:</label>
                                <div class="col-sm-4">
                                    <select wire:model.live="statusFilter" class="form-control">
                                        <option value="">All</option>
                                        <option value="newly_submitted">Newly Submitted</option>
                                        <option value="pending">Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="awaiting_additional_info">Awaiting Additional Info</option>
                                        <option value="forwarded_to_training_officer">Forwarded to Training Officer</option>
                                        <option value="forwarded_to_jtc_member">Forwarded to JTC Member</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Applicant</th>
                                            <th>Status</th>
                                            <th>Submitted At</th>
                                            <th>Time Since Submission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($applications as $application)
                                            <tr>
                                                <td>{{ $application->id }}</td>
                                                <td>{{ $application->user->name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-info text-capitalize">{{ str_replace(['_', '-'], ' ', $application->status) }}</span></td>
                                                <td>{{ $application->submitted_at->format('Y-m-d H:i') }}</td>
                                                <td>{{ $application->submitted_at->diffForHumans() }}</td>
                                                <td>
                                                    <button wire:click="viewDetails({{ $application->id }})" class="btn btn-sm btn-info">
                                                        View Details
                                                    </button>
                                                    @if (auth()->user()->role === 'clerk' && $application->status === 'newly_submitted')
                                                        <button wire:click="forwardApplication({{ $application->id }})" class="btn btn-sm btn-success">
                                                            Forward
                                                        </button>
                                                    @elseif (auth()->user()->role === 'training_officer' && $application->status === 'forwarded_to_training_officer')
                                                        <button wire:click="forwardApplication({{ $application->id }})" class="btn btn-sm btn-success">
                                                            Forward
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No applications found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $applications->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
