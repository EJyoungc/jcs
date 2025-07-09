<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Document Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Documents</li>
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
                                Add Document <x-spinner for="create" />
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Application ID</th>
                                            <th>File Path</th>
                                            <th>File Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $document)
                                            <tr>
                                                <td>{{ $document->application->id ?? 'N/A' }}</td>
                                                <td>{{ $document->file_path }}</td>
                                                <td><span class="badge badge-info text-capitalize">{{ $document->file_type }}</span></td>
                                                <td>
                                                    <button wire:click="create('{{ $document->id }}')"
                                                        class="btn btn-sm btn-warning">
                                                        Edit
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $document->id }})"
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

    <x-modal title="{{ $selectedDocument ? 'Edit Document: ' . $selectedDocument->id : 'Add Document' }}" :status="$modal">
        <form wire:submit.prevent="store">
            <div class="form-group">
                <label for="application_id">Application</label>
                <select class="form-control" wire:model='application_id'>
                    <option value="">Select Application</option>
                    @foreach($applications as $application)
                        <option value="{{ $application->id }}">{{ $application->id }}</option>
                    @endforeach
                </select>
                <x-error for="application_id" />
            </div>
            <div class="form-group">
                <label for="file_path">File Path</label>
                <input type="text" class="form-control" wire:model='file_path'>
                <x-error for="file_path" />
            </div>
            <div class="form-group">
                <label for="file_type">File Type</label>
                <input type="text" class="form-control" wire:model='file_type'>
                <x-error for="file_type" />
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">
                    Save <x-spinner for="store" />
                </button>
            </div>
        </form>
    </x-modal>
</div>