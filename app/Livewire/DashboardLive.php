<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Application;
use App\Models\Recommendation;
use App\Models\AuditLog;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class DashboardLive extends Component
{
    public $dashboardData = [];
    public $recentActivities = [];
    public $applicationStatusChartData = [];
    public $applicationsOverTimeChartData = [];

    protected $listeners = ['echo:audit-logs,AuditLogCreated' => 'loadRecentActivities'];

    public function mount()
    {
        $this->loadDashboardData();
        $this->loadRecentActivities();
        $this->loadChartData();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();
        $role = $user->role;

        switch ($role) {
            case 'candidate':
                $this->dashboardData = [
                    'applications' => $user->applications()->orderBy('created_at', 'desc')->get(),
                ];
                break;
            case 'district_court_clerk':
            case 'training_officer':
            case 'jtc_member':
                $this->dashboardData = [
                    'pending_applications_count' => Application::where('status', 'pending_review')->count(), // Example, adjust based on actual assignment logic
                    'personal_review_workload_count' => Review::where('reviewer_id', $user->id)->where('status', 'pending')->count(),
                ];
                break;
            case 'chairperson':
                $this->dashboardData = [
                    'pending_recommendations_count' => Recommendation::where('status', 'pending_chairperson_review')->count(),
                ];
                break;
            case 'system_administrator':
                $this->dashboardData = [
                    'total_applications' => Application::count(),
                    'pending_applications' => Application::where('status', 'pending_committee_vote')->count(), // Adjust status as needed
                    'approved_applications' => Application::where('status', 'approved')->count(),
                    'rejected_applications' => Application::where('status', 'rejected')->count(),
                ];
                break;
            default:
                $this->dashboardData = [];
                break;
        }
    }

    public function loadRecentActivities()
    {
        $this->recentActivities = AuditLog::orderBy('created_at', 'desc')->take(5)->get();
    }

    public function loadChartData()
    {
        if (Auth::user()->role === 'system_administrator') {
            // Application Status Distribution Chart Data
            $statusCounts = Application::select('status')
                                        ->selectRaw('count(*) as count')
                                        ->groupBy('status')
                                        ->pluck('count', 'status')
                                        ->toArray();

            $this->applicationStatusChartData = [
                'labels' => array_map(function($status) { return ucfirst(str_replace('_', ' ', $status)); }, array_keys($statusCounts)),
                'data' => array_values($statusCounts),
                'colors' => [
                    '#007bff', // pending_committee_vote (blue)
                    '#28a745', // approved (green)
                    '#dc3545', // rejected (red)
                    '#ffc107', // other statuses (yellow)
                    '#6c757d', // default (gray)
                ]
            ];

            // Applications Over Time Chart Data (e.g., last 12 months)
            $months = [];
            $applicationsCount = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $months[] = $month->format('M Y');
                $applicationsCount[] = Application::whereYear('created_at', $month->year)
                                                ->whereMonth('created_at', $month->month)
                                                ->count();
            }

            $this->applicationsOverTimeChartData = [
                'labels' => $months,
                'data' => $applicationsCount,
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard-live', [
            'userRole' => Auth::user()->role,
        ]);
    }
}
