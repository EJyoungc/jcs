<?php

namespace App\Livewire\ApplicationSubmission;

use App\Models\Application;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class ApplicationSubmissionLivewire extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $modal = false;
    public $applications;
    public $selectedApplication;

    // form fields
    public $status, $submitted_at, $reviewed_at;
    public $documents = [];
    public $isUploading = false;

    public function uploading($name, $tmpPath)
    {
        $this->isUploading = true;
    }

    public function uploaded($name, $tmpPath)
    {
        $this->isUploading = false;
    }

    public function error($name, $tmpPath, $error)
    {
        $this->isUploading = false;
    }

    protected function rules()
    {
        return [
            'documents.*' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB in kilobytes
        ];
    }


    public function mount(){
        $this->user_id = Auth::user()->id;
    }

    public function create($id = null)
    {
        if (empty($id)) {
            $this->reset(['selectedApplication', 'status', 'submitted_at', 'reviewed_at']);
            $this->modal = true;
        } else {
            $this->selectedApplication = Application::findOrFail($id);
            $this->user_id = $this->selectedApplication->user_id;
            $this->status = $this->selectedApplication->status;
            $this->submitted_at = $this->selectedApplication->submitted_at;
            $this->reviewed_at = $this->selectedApplication->reviewed_at;
            $this->modal = true;
        }
    }

    public function store()
    {
        if ($this->isUploading) {
            $this->alert('warning', 'Please wait for file uploads to complete.');
            return;
        }

        $rules = [
            // 'status' => 'required|string|max:255',
            'submitted_at' => 'nullable|date',
            'reviewed_at' => 'nullable|date',
        ];

        $this->validate();

        DB::transaction(function () {
            if (empty($this->selectedApplication)) {
                $application = Application::create([
                    'user_id' => Auth::user()->id,
                    'submitted_at' => now(),
                    'status' => 'newly_submitted',
                ]);
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Application Submitted',
                    'description' => 'New application submitted by ' . Auth::user()->name . '.',
                    'auditable_type' => Application::class,
                    'auditable_id' => $application->id,
                ]);
                $this->alert('success', 'Application created successfully.');
            } else {
                $application = $this->selectedApplication;
                $application->update([
                    'submitted_at' => now(),
                ]);
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Application Updated',
                    'description' => 'Application ' . $application->id . ' updated by ' . Auth::user()->name . '.',
                    'auditable_type' => Application::class,
                    'auditable_id' => $application->id,
                ]);
                $this->alert('success', 'Application updated successfully.');
            }

            // Ensure we have the latest application instance with its ID
            $application->refresh();

            foreach ($this->documents as $document) {
                $originalName = $document->getClientOriginalName();
                $mimeType = $document->getMimeType();
                $path = $document->store('documents', 'public'); // Store in 'storage/app/public/documents'

                Document::create([
                    'application_id' => $application->id,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'mime_type' => $mimeType,
                ]);
            }

            // Notify clerks
            $clerks = User::where('role', 'district_court_clerk')->get();
            foreach ($clerks as $clerk) {
                $clerk->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\\Notifications\\ApplicationSubmitted',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $clerk->id,
                    'data' => [
                        'application_id' => $application->id,
                        'message' => 'A new application has been submitted.',
                        'url' => route('application-review', $application->id),
                    ],
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->alert('success', 'Application submitted successfully and documents uploaded.');

        $this->reset('documents');

        $this->cancel();
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $application = Application::findOrFail($id);

            // Delete associated documents from storage and database
            foreach ($application->documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }

            // Delete the application
            $application->delete();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Application Deleted',
                'description' => 'Application ' . $application->id . ' and its documents deleted by ' . Auth::user()->name . '.',
                'auditable_type' => Application::class,
                'auditable_id' => $application->id,
            ]);

            $this->alert('success', 'Application deleted successfully.');
        });
    }



    public function cancel()
    {
        $this->reset(['modal', 'selectedApplication',  'status', 'submitted_at', 'reviewed_at', 'documents']);
        $this->dispatch('modal-cancel');
    }

    public function render()
    {
        $this->applications = Application::with('user')->get();
        return view('livewire.application-submission.application-submission-livewire', [
            'users' => User::all(), // Pass users for dropdown
        ]);
    }
}
