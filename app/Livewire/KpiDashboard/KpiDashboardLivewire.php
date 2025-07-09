<?php

namespace App\Livewire\KpiDashboard;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Review;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;



class KpiDashboardLivewire extends Component
{
    use LivewireAlert;

    public $startDate;
    public $endDate;
    public $period = 'monthly'; // daily, weekly, monthly

    public $averageProcessingTime = 0;
    public $applicationsSubmittedPerMonth = ['labels' => [], 'data' => []];
    public $backlogVolume = 0;
    public $workloadDistribution = ['labels' => [], 'data' => []];
    public $approvalRejectionTrends = ['labels' => [], 'approved' => [], 'rejected' => []];

    protected $queryString = ['startDate', 'endDate', 'period'];

    public function boot()
    {
        // Limit access to Admin and Chairperson roles only
        if (!in_array(Auth::user()->role, ['system_administrator', 'chairperson'])) {
            // abort(403, 'Unauthorized access.');
        }
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'KPI Dashboard Access',
            'description' => 'User ' . Auth::user()->name . ' accessed the KPI Dashboard.',
            'auditable_type' => null,
            'auditable_id' => null,
        ]);
    }

    public function mount()
    {
        $this->setInitialDateRange();
        $this->loadKpiData();
    }

    public function updatedPeriod()
    {
        $this->setInitialDateRange();
        $this->loadKpiData();
    }

    public function updatedStartDate()
    {
        $this->loadKpiData();
    }

    public function updatedEndDate()
    {
        $this->loadKpiData();
    }

    private function setInitialDateRange()
    {
        switch ($this->period) {
            case 'daily':
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                break;
            case 'weekly':
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                $this->startDate = Carbon::now()->subWeeks(12)->startOfWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                $this->startDate = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
                break;
        }
    }

    private function loadKpiData()
    {
        $this->calculateAverageProcessingTime();
        $this->getApplicationsSubmittedPerPeriod();
        $this->getBacklogVolume();
        $this->getWorkloadDistributionPerReviewer();
        $this->getApprovalRejectionTrends();
    }

    private function calculateAverageProcessingTime()
    {
        $applications = Application::whereNotNull('submitted_at')
                                    ->whereNotNull('reviewed_at') // Assuming reviewed_at marks completion
                                    ->whereBetween('submitted_at', [$this->startDate, $this->endDate])
                                    ->get();

        $totalProcessingTime = 0;
        $count = 0;

        foreach ($applications as $app) {
            if ($app->submitted_at && $app->reviewed_at) {
                $totalProcessingTime += $app->submitted_at->diffInDays($app->reviewed_at);
                $count++;
            }
        }

        $this->averageProcessingTime = $count > 0 ? round($totalProcessingTime / $count, 2) : 0;
    }

    private function getApplicationsSubmittedPerPeriod()
    {
        $query = Application::selectRaw("strftime('%Y-%m', created_at) as period, count(*) as count")
                            ->whereBetween('created_at', [$this->startDate, $this->endDate])
                            ->groupBy('period')
                            ->orderBy('period')
                            ->get();

        $labels = [];
        $data = [];

        foreach ($query as $item) {
            $labels[] = $item->period;
            $data[] = $item->count;
        }

        $this->applicationsSubmittedPerMonth = ['labels' => $labels, 'data' => $data];
    }

    private function getBacklogVolume()
    {
        $this->backlogVolume = Application::whereIn('status', ['pending_review', 'under_review', 'forwarded', 'awaiting_additional_info'])
                                        ->whereBetween('created_at', [$this->startDate, $this->endDate])
                                        ->count();
    }

    private function getWorkloadDistributionPerReviewer()
    {
        $reviews = Review::selectRaw('reviewer_id, count(*) as count')
                        ->where('status', 'pending') // Assuming 'pending' reviews constitute workload
                        ->whereBetween('created_at', [$this->startDate, $this->endDate])
                        ->groupBy('reviewer_id')
                        ->with('reviewer') // Eager load reviewer to get their name
                        ->get();

        $labels = [];
        $data = [];

        foreach ($reviews as $review) {
            $labels[] = $review->reviewer->name ?? 'N/A';
            $data[] = $review->count;
        }

        $this->workloadDistribution = ['labels' => $labels, 'data' => $data];
    }

    private function getApprovalRejectionTrends()
    {
        $approved = Application::selectRaw("strftime('%Y-%m', reviewed_at) as period, count(*) as count")
                                ->where('status', 'approved')
                                ->whereBetween('reviewed_at', [$this->startDate, $this->endDate])
                                ->groupBy('period')
                                ->orderBy('period')
                                ->get();

        $rejected = Application::selectRaw("strftime('%Y-%m', reviewed_at) as period, count(*) as count")
                                ->where('status', 'rejected')
                                ->whereBetween('reviewed_at', [$this->startDate, $this->endDate])
                                ->groupBy('period')
                                ->orderBy('period')
                                ->get();

        $labels = array_unique(array_merge($approved->pluck('period')->toArray(), $rejected->pluck('period')->toArray()));
        sort($labels);

        $approvedData = [];
        $rejectedData = [];

        foreach ($labels as $label) {
            $approvedData[] = $approved->where('period', $label)->first()->count ?? 0;
            $rejectedData[] = $rejected->where('period', $label)->first()->count ?? 0;
        }

        $this->approvalRejectionTrends = [
            'labels' => $labels,
            'approved' => $approvedData,
            'rejected' => $rejectedData,
        ];
    }

    public function exportPdf()
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Export KPI Report',
            'description' => 'User ' . Auth::user()->name . ' exported KPI report to PDF.',
            'auditable_type' => null,
            'auditable_id' => null,
        ]);

        // Example of how to generate PDF. You will need to create a Blade view
        // (e.g., 'kpi.report_pdf') that formats your KPI data.
        $data = [
            'averageProcessingTime' => $this->averageProcessingTime,
            'applicationsSubmittedPerMonth' => $this->applicationsSubmittedPerMonth,
            'backlogVolume' => $this->backlogVolume,
            'workloadDistribution' => $this->workloadDistribution,
            'approvalRejectionTrends' => $this->approvalRejectionTrends,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'period' => $this->period,
        ];

        $pdf = Pdf::loadView('kpi.report_pdf', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'kpi_report_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv()
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Export KPI Report',
            'description' => 'User ' . Auth::user()->name . ' exported KPI report to CSV.',
            'auditable_type' => null,
            'auditable_id' => null,
        ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="kpi_report_' . Carbon::now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['KPI', 'Value']);
            fputcsv($file, ['Average Processing Time (Days)', $this->averageProcessingTime]);
            fputcsv($file, ['Backlog Volume', $this->backlogVolume]);

            fputcsv($file, []); // Empty row for separation
            fputcsv($file, ['Applications Submitted Per Period']);
            fputcsv($file, ['Period', 'Count']);
            foreach ($this->applicationsSubmittedPerMonth['labels'] as $index => $label) {
                fputcsv($file, [$label, $this->applicationsSubmittedPerMonth['data'][$index]]);
            }

            fputcsv($file, []); // Empty row for separation
            fputcsv($file, ['Workload Distribution Per Reviewer']);
            fputcsv($file, ['Reviewer', 'Pending Reviews']);
            foreach ($this->workloadDistribution['labels'] as $index => $label) {
                fputcsv($file, [$label, $this->workloadDistribution['data'][$index]]);
            }

            fputcsv($file, []); // Empty row for separation
            fputcsv($file, ['Approval vs. Rejection Trends']);
            fputcsv($file, ['Period', 'Approved', 'Rejected']);
            foreach ($this->approvalRejectionTrends['labels'] as $index => $label) {
                fputcsv($file, [
                    $label,
                    $this->approvalRejectionTrends['approved'][$index],
                    $this->approvalRejectionTrends['rejected'][$index]
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.kpi-dashboard.kpi-dashboard-livewire');
    }
}
