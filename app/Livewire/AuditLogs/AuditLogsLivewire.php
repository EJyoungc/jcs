<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AuditLogsLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';
    public $filterByUser = '';
    public $filterByAction = '';
    public $filterByStartDate = '';
    public $filterByEndDate = '';

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
        'filterByUser' => ['except' => '', 'as' => 'user'],
        'filterByAction' => ['except' => '', 'as' => 'action'],
        'filterByStartDate' => ['except' => '', 'as' => 'start_date'],
        'filterByEndDate' => ['except' => '', 'as' => 'end_date'],
    ];

    public function boot()
    {
        // Limit access to Admin and Chairperson roles only
        if (!in_array(Auth::user()->role, ['system_administrator', 'chairperson'])) {
            // abort(403, 'Unauthorized access.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterByUser()
    {
        $this->resetPage();
    }

    public function updatingFilterByAction()
    {
        $this->resetPage();
    }

    public function updatingFilterByStartDate()
    {
        $this->resetPage();
    }

    public function updatingFilterByEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $auditLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply search functionality
        if (!empty($this->search)) {
            $auditLogs->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('action', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('auditable_id', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if (!empty($this->filterByUser)) {
            $auditLogs->where('user_id', $this->filterByUser);
        }

        if (!empty($this->filterByAction)) {
            $auditLogs->where('action', 'like', '%' . $this->filterByAction . '%');
        }

        if (!empty($this->filterByStartDate)) {
            $auditLogs->whereDate('created_at', '>=', $this->filterByStartDate);
        }

        if (!empty($this->filterByEndDate)) {
            $auditLogs->whereDate('created_at', '<=', $this->filterByEndDate);
        }

        return view('livewire.audit-logs.audit-logs-livewire', [
            'auditLogs' => $auditLogs->paginate(10),
            'users' => User::all(), // For user filter dropdown
            'actionTypes' => AuditLog::select('action')->distinct()->pluck('action'), // For action type filter dropdown
        ]);
    }
}
