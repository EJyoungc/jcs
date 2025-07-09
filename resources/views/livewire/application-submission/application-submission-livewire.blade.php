<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Application Submission Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Application Submissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end ">
                        <div class="form-group">
                            <button @click="$wire.create(); $wire.dispatch('modal-open');"
                                class="btn btn-primary btn-sm">
                                Add Application <x-spinner for="create" />
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Submitted At</th>
                                            <th>Reviewed At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($applications as $application)
                                            <tr>
                                                <td>{{ $application->user->name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-info text-capitalize">{{ $application->status }}</span></td>
                                                <td>{{ $application->submitted_at }}</td>
                                                <td>{{ $application->reviewed_at }}</td>
                                                <td>
                                                    <button wire:click="create('{{ $application->id }}')"
                                                        class="btn btn-sm btn-warning">
                                                        Edit
                                                    </button>
                                                    <button wire:click="delete({{ $application->id }})" wire:confirm="Are you sure you want to delete this application?"
                                                        class="btn btn-sm btn-danger">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-modal title="{{ $selectedApplication ? 'Edit Application: ' . $selectedApplication->id : 'Add Application' }}" :status="$modal">
        <form wire:submit.prevent="store">
            
            {{-- <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" wire:model='status'>
                <x-error for="status" />
            </div> --}}
           

            <div class="form-group">
                <label for="documents">Upload Documents (Word or PDF, max 10MB each)</label>
                <input type="file" class="form-control-file" wire:model="documents" multiple>
                <x-error for="documents.*" />

                <div wire:loading wire:target="documents">
                    Uploading files... <x-spinner for="documents" />
                </div>

                @if ($documents)
                    <div class="mt-2">
                        @foreach ($documents as $document)
                            <div class="d-flex align-items-center mb-1">
                                @if (in_array($document->extension(), ['doc', 'docx']))
                                    <i class="far fa-file-word fa-2x mr-2"></i>
                                @elseif (in_array($document->extension(), ['pdf']))
                                    <i class="far fa-file-pdf fa-2x mr-2"></i>
                                @endif
                                <span>{{ $document->getClientOriginalName() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store, documents" :disabled="$isUploading">
                    Save <x-spinner for="store" />
                </button>
            </div>
        </form>
    </x-modal>
</div>
