<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Applications Pending Committee Votes</h3>
                </div>
                <div class="card-body">
                    @if ($applications->isEmpty())
                        <p>No applications currently pending committee votes.</p>
                    @else
                        @foreach ($applications as $application)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Application ID: {{ $application->id }}</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Status:</strong> {{ $application->status }}</p>
                                    <p><strong>Submitted By:</strong> {{ $application->user->name ?? 'N/A' }}</p>
                                    <p><strong>Voted Members:</strong> {{ $votedMembersCount[$application->id] ?? 0 }} / {{ $totalJtcMembers }}</p>

                                    @if ($application->voting_outcome)
                                        <p><strong>Voting Outcome:</strong> <span class="badge badge-{{ $application->voting_outcome == 'approved' ? 'success' : ($application->voting_outcome == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $application->voting_outcome)) }}</span></p>
                                    @else
                                        @if ($userVote[$application->id] === null)
                                            <div class="form-group">
                                                <label for="comment-{{ $application->id }}">Your Comment:</label>
                                                <textarea wire:model.defer="comment.{{ $application->id }}" id="comment-{{ $application->id }}" class="form-control" rows="2"></textarea>
                                                @error('comment.' . $application->id) <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mt-3">
                                                <button wire:click="castVote({{ $application->id }}, 'Approve')" class="btn btn-success mr-2">
                                                    Approve <x-spinner for="castVote({{ $application->id }}, 'Approve')" />
                                                </button>
                                                <button wire:click="castVote({{ $application->id }}, 'Reject')" class="btn btn-danger mr-2">
                                                    Reject <x-spinner for="castVote({{ $application->id }}, 'Reject')" />
                                                </button>
                                                <button wire:click="castVote({{ $application->id }}, 'Request Revision')" class="btn btn-warning">
                                                    Request Revision <x-spinner for="castVote({{ $application->id }}, 'Request Revision')" />
                                                </button>
                                            </div>
                                        @else
                                            <p>You have already voted: <span class="badge badge-info">{{ $userVote[$application->id] }}</span></p>
                                            @if (!empty($comment[$application->id]))
                                                <p>Your Comment: {{ $comment[$application->id] }}</p>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>