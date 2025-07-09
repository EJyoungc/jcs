<?php

namespace App\Livewire\ApplicationReview;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Document;
use App\Models\Review;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationReviewLivewire extends Component
{
    use LivewireAlert;

    public $applicationId;
    public $application;
    public $comments;
    public $newStatus;
    public $forwardToUser;
    public $forwardToCommittee = false;
    public $reviewers;
    public $showPreviewModal = false;

    protected $listeners = ['modal-cancel' => 'closePreviewModal'];


    protected $rules = [
        'comments' => 'nullable|string',
        'newStatus' => 'required|string|in:under_review,forwarded,awaiting_additional_info,completed',
        'forwardToUser' => 'nullable|exists:users,id',
        'forwardToCommittee' => 'boolean',
    ];

    protected $messages = [
        'newStatus.in' => 'Invalid status selected.',
        'forwardToUser.exists' => 'Selected reviewer does not exist.',
    ];

    public function mount($applicationId)
    {
        $this->applicationId = $applicationId;
        $this->loadApplication();
        $this->reviewers = User::where('id', '!=', Auth::id())->get(); // Get all users except current for forwarding
    }

    public function loadApplication()
    {
        $this->application = Application::with(['user', 'documents', 'reviews.reviewer'])->findOrFail($this->applicationId);
        $this->newStatus = $this->application->status; // Set initial status to current application status
    }

    public function saveReview()
    {
        $this->validate(['comments' => 'required|string']);

        Review::create([
            'application_id' => $this->application->id,
            'reviewer_id' => Auth::id(),
            'comments' => $this->comments,
            'status' => $this->application->status, // Status at the time of review
        ]);

        $this->logAudit('add_review_comment', 'Added review comment', $this->application->id, ['comment' => $this->comments]);
        $this->comments = ''; // Clear comments after saving
        $this->alert('success', 'Review comment added successfully.');
        $this->loadApplication(); // Refresh application data to show new comment
    }

    public function updateApplicationStatus()
    {
        $this->validate(['newStatus' => 'required|string|in:under_review,forwarded,awaiting_additional_info,completed']);

        $oldStatus = $this->application->status;
        $this->application->update(['status' => $this->newStatus]);

        $this->logAudit('update_application_status', 'Updated application status', $this->application->id, [
            'old_status' => $oldStatus,
            'new_status' => $this->newStatus,
        ]);

        $this->alert('success', 'Application status updated successfully.');
        $this->loadApplication(); // Refresh application data
    }

    public function forwardApplication()
    {
        $rules = [
            'newStatus' => 'required|string|in:forwarded',
            'forwardToUser' => 'required_if:forwardToCommittee,false|nullable|exists:users,id',
            'forwardToCommittee' => 'boolean',
        ];

        $this->validate($rules);

        $oldStatus = $this->application->status;
        $forwardTarget = null;

        if ($this->forwardToCommittee) {
            $this->application->update(['status' => 'forwarded_to_jtc_committee']);
            $forwardTarget = 'JTC Committee';
            $usersToNotify = User::where('role', 'jtc_member')->get();
            $message = 'An application has been forwarded to the JTC Committee for review.';
            foreach ($usersToNotify as $user) {
                $user->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\\Notifications\\ApplicationForwarded',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'data' => [
                        'application_id' => $this->application->id,
                        'message' => $message,
                        'url' => route('application-review', ['applicationId' => $this->application->id]),
                    ],
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($this->forwardToUser) {
            $this->application->update(['status' => 'forwarded', 'reviewer_id' => $this->forwardToUser]); // Assuming reviewer_id on application model
            $forwardTarget = User::find($this->forwardToUser)->name ?? 'Unknown User';
            $message = 'An application has been forwarded to ' . $forwardTarget . ' for review.';
            $userToNotify = User::find($this->forwardToUser);
            $userToNotify->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ApplicationForwarded',
                'notifiable_type' => User::class,
                'notifiable_id' => $userToNotify->id,
                'data' => [
                    'application_id' => $this->application->id,
                    'message' => $message,
                    'url' => route('application-review', ['applicationId' => $this->application->id]),
                ],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->logAudit('forward_application', 'Forwarded application', $this->application->id, [
            'old_status' => $oldStatus,
            'new_status' => $this->application->status,
            'forward_target' => $forwardTarget,
        ]);

        $this->alert('success', 'Application forwarded successfully.');
        $this->loadApplication(); // Refresh application data
    }

    private function logAudit(string $action, string $description, int $auditableId = null, array $details = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'auditable_type' => Application::class,
            'auditable_id' => $auditableId,
            'timestamp' => now(),
            'details' => json_encode($details), // Store additional details as JSON
        ]);
    }



    public function previewDocument(Document $document)
    {

        try {
            $filePath = $document->file_path;
            $mimeType = $document->mime_type;

            if ($mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimeType === 'application/msword') {
                // DOCX preview
                $url = Storage::disk('public')->url(
                    $filePath
                );
                $this->dispatch('open-document-preview', url: $url, mimeType: $mimeType);
                // $this->alert('success', 'Opening DOCX preview.');
            } elseif ($mimeType === 'application/pdf') {
                // PDF preview
                $url = Storage::disk('public')->url(
                    $document->file_path
                );
                $this->dispatch('open-document-preview', url: $url, mimeType: $mimeType);
                // $this->alert('success', 'Opening PDF preview.');
            } else {
                // Unsupported file type for preview
                $this->logAudit('preview_document_error', 'Unsupported file type for preview', $document->id, [
                    'mime_type' => $mimeType,
                ]);
                $this->dispatch('close-document-preview');

                $this->alert('warning', 'Preview not available for this file type.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the preview generation
            $this->logAudit('preview_document_error', 'Error generating document preview', $document->id, [
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('close-document-preview');

            $this->alert('error', 'Could not generate preview: ' . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.application-review.application-review-livewire');
    }


}
