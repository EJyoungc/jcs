<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recommendations Pending Chairperson Review</h3>
                </div>
                <div class="card-body">
                    @if ($recommendations->isEmpty())
                        <p>No recommendations currently pending chairperson review.</p>
                    @else
                        @foreach ($recommendations as $recommendation)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Application ID: {{ $recommendation->application->id }}</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Recommendation:</strong> {{ $recommendation->recommendation_text }}</p>
                                    <p><strong>Current Status:</strong> <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $recommendation->status)) }}</span></p>

                                    <h6>Voting History:</h6>
                                    @if ($recommendation->application->committeeVotes->isEmpty())
                                        <p>No voting history available.</p>
                                    @else
                                        <ul class="list-group mb-3">
                                            @foreach ($recommendation->application->committeeVotes as $vote)
                                                <li class="list-group-item">
                                                    <strong>Member:</strong> {{ $vote->member->name ?? 'N/A' }}<br>
                                                    <strong>Vote:</strong> <span class="badge badge-{{ $vote->vote == 'Approve' ? 'success' : ($vote->vote == 'Reject' ? 'danger' : 'warning') }}">{{ $vote->vote }}</span><br>
                                                    <strong>Comment:</strong> {{ $vote->comment ?? 'No comment' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <div class="mt-3">
                                        <button wire:click="approveRecommendation({{ $recommendation->id }})" class="btn btn-success mr-2">
                                            Approve <x-spinner for="approveRecommendation({{ $recommendation->id }})" />
                                        </button>
                                        <button wire:click="rejectRecommendation({{ $recommendation->id }})" class="btn btn-danger mr-2">
                                            Reject <x-spinner for="rejectRecommendation({{ $recommendation->id }})" />
                                        </button>
                                        <button wire:click="requestRevisionRecommendation({{ $recommendation->id }})" class="btn btn-warning mr-2">
                                            Request Revision <x-spinner for="requestRevisionRecommendation({{ $recommendation->id }})" />
                                        </button>
                                        <button wire:click="downloadReport({{ $recommendation->id }})" class="btn btn-info">
                                            Download Report <x-spinner for="downloadReport({{ $recommendation->id }})" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>