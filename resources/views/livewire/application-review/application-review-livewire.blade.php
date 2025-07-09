<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Application Review</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Application Review</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if ($application)
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Application Details #{{ $application->id }}</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Applicant:</strong> {{ $application->user->name ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> <span class="badge badge-info text-capitalize">{{ str_replace(['_', '-'], ' ', $application->status) }}</span></p>
                                <p><strong>Submitted At:</strong> {{ $application->submitted_at->format('Y-m-d H:i') }}</p>
                                <p><strong>Time Since Submission:</strong> {{ $application->submitted_at->diffForHumans() }}</p>

                                <hr>

                                <h4>Submitted Documents</h4>
                                @forelse ($application->documents as $document)
                                    <div class="d-flex align-items-center mb-2">
                                        @if (in_array(pathinfo($document->file_name, PATHINFO_EXTENSION), ['doc', 'docx']))
                                            <i class="far fa-file-word fa-2x mr-2"></i>
                                        @elseif (in_array(pathinfo($document->file_name, PATHINFO_EXTENSION), ['pdf']))
                                            <i class="far fa-file-pdf fa-2x mr-2"></i>
                                        @else
                                            <i class="far fa-file fa-2x mr-2"></i>
                                        @endif
                                        <span>{{ $document->file_name }} ({{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }})</span>
                                        <a href="{{ route('documents.download', $document->id) }}" class="btn btn-sm btn-primary ml-3" target="_blank">Download</a>
                                        <button wire:click="previewDocument({{ $document->id }})" class="btn btn-sm btn-info ml-2">View</button>
                                    </div>
                                @empty
                                    <p>No documents submitted for this application.</p>
                                @endforelse

                                <hr>

                                <h4>Review History</h4>
                                @forelse ($application->reviews as $review)
                                    <div class="card card-light card-outline mb-2">
                                        <div class="card-header">
                                            <h5 class="card-title">Review by {{ $review->reviewer->name ?? 'N/A' }} on {{ $review->created_at->format('Y-m-d H:i') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>{{ $review->comments }}</p>
                                            <p><small>Status at review: {{ str_replace(['_', '-'], ' ', $review->status) }}</small></p>
                                        </div>
                                    </div>
                                @empty
                                    <p>No review history for this application.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Review Actions</h3>
                            </div>
                            <div class="card-body">
                                <form wire:submit.prevent="saveReview">
                                    <div class="form-group">
                                        <label for="comments">Add Annotation/Comment</label>
                                        <textarea wire:model.live="comments" class="form-control" rows="3" placeholder="Add your comments here..."></textarea>
                                        @error('comments') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Comment</button>
                                </form>

                                <hr>

                                <form wire:submit.prevent="updateApplicationStatus">
                                    <div class="form-group">
                                        <label for="newStatus">Update Status</label>
                                        <select wire:model.live="newStatus" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="under_review">Under Review</option>
                                            <option value="forwarded">Forwarded</option>
                                            <option value="awaiting_additional_info">Awaiting Additional Info</option>
                                            <option value="completed">Completed</option>

                                            @if (Auth::user()->role == "Chairperson")
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                          

                                            @endif

                                        </select>
                                        @error('newStatus') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="btn btn-info">Update Status</button>
                                </form>

                                <hr>

                                <form wire:submit.prevent="forwardApplication">
                                    <h5>Forward Application</h5>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model.live="forwardToCommittee" id="forwardToCommittee">
                                            <label class="form-check-label" for="forwardToCommittee">
                                                Forward to JTC Committee
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group" x-data="{}" x-show="!$wire.forwardToCommittee">
                                        <label for="forwardToUser">Forward to Specific Reviewer</label>
                                        <select wire:model.live="forwardToUser" class="form-control">
                                            <option value="">Select Reviewer</option>
                                            @foreach($reviewers as $reviewer)
                                                <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('forwardToUser') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">Forward</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    Application not found or not accessible.
                </div>
            @endif
        </div>
    </section>

    </section>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                Livewire.on('open-external-document', (event) => {
                    window.open(event.url, '_blank');
                });
            });
        </script>
    @endpush
</div>
