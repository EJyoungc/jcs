<?php

namespace App\Livewire\ApplicationQueue;

use App\Models\Application;
use App\Models\User;
use App\Models\AuditLog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationQueueLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewDetails(Application $application)
    {
        $this->logAudit('view_details', 'Viewed application details', $application->id);
        // Redirect to application details page or open a modal
        // Example: return redirect()->route('applications.show', $application->id);
        $this->alert('info', 'Viewing details for Application ID: ' . $application->id);
        sleep(2);
        return redirect()->route('application-review', $application->id); // This will redirect to the review page
    }

    public function forwardApplication(Application $application)
    {
        // Logic to forward application based on role
        $user = Auth::user();
        $newStatus = '';
        $logMessage = '';

        if ($user->role === 'district_court_clerk' && $application->status === 'newly_submitted') {
            $newStatus = 'forwarded_to_training_officer';
            $logMessage = 'Clerk forwarded application to Training Officer';
        } elseif ($user->role === 'training_officer' && $application->status === 'forwarded_to_training_officer') {
            $newStatus = 'forwarded_to_jtc_member';
            $logMessage = 'Training Officer forwarded application to JTC Member';
        } else {
            $this->alert('error', 'Cannot forward this application.');
            return;
        }

        $application->update(['status' => $newStatus]);
        $this->logAudit('forward_application', $logMessage, $application->id);

        // Notify the next role
        if ($newStatus === 'forwarded_to_training_officer') {
            $usersToNotify = User::where('role', 'training_officer')->get();
            $message = 'An application has been forwarded to you for review.';
        } elseif ($newStatus === 'forwarded_to_jtc_member') {
            $usersToNotify = User::where('role', 'jtc_member')->get();
            $message = 'An application has been forwarded to you for review.';
        }

        if (isset($usersToNotify)) {
            foreach ($usersToNotify as $userToNotify) {
                $userToNotify->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\\Notifications\\ApplicationForwarded',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $userToNotify->id,
                    'data' => [
                        'application_id' => $application->id,
                        'message' => $message,
                        'url' => route('application-review', ['id' => $application->id]),
                    ],
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->alert('success', 'Application forwarded successfully.');
    }

    private function logAudit(string $action, string $description, int $applicationId = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'auditable_type' => Application::class,
            'auditable_id' => $applicationId,
            'timestamp' => now(),
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $applications = Application::query();

        // Role-based filtering
        if ($user->role === 'district_court_clerk') {
            $applications->where('status', 'newly_submitted');
        } elseif ($user->role === 'training_officer') {
            $applications->where('status', 'forwarded_to_training_officer');
        } elseif ($user->role === 'jtc_member') {
            $applications->where('status', 'forwarded_to_jtc_member');
        }

        // Search filter
        if ($this->search) {
            $applications->where(function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $applications->where('status', $this->statusFilter);
        }

        $applications = $applications->with('user')
                                     ->orderBy('submitted_at', 'desc')
                                     ->paginate($this->perPage);

        return view('livewire.application-queue.application-queue-livewire', [
            'applications' => $applications,
        ]);
    }
}
