<?php


use App\Livewire\DashboardLive;

use App\Livewire\Profile\ProfileLive;

use App\Livewire\Repots\ReportLive;

use App\Livewire\Users\UserLive;
use App\Livewire\ApplicationQueue\ApplicationQueueLivewire;
use App\Livewire\ApplicationReview\ApplicationReviewLivewire;
use App\Livewire\ApplicationSubmission\ApplicationSubmissionLivewire;
use App\Livewire\AuditLogs\AuditLogsLivewire;
use App\Livewire\CommitteeVoting\CommitteeVotingLivewire;
use App\Livewire\KpiDashboard\KpiDashboardLivewire;
use App\Livewire\Notifications\NotificationsLivewire;
use App\Livewire\RecommendationReview\RecommendationReviewLivewire;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get("/", function () {
    return view("welcome");
})->name('dashboard');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/use/profile', ProfileLive::class)->name('profile');

    // Dashboard is accessible by all authenticated users
    Route::get('/dashboard', DashboardLive::class)->name('dashboard');

    // Candidate Routes
    Route::get('/application-submission', ApplicationSubmissionLivewire::class)->name('application-submission');

    Route::get('/audit-logs', AuditLogsLivewire::class)->name('audit-logs');
    Route::get('/application-review/{applicationId}', ApplicationReviewLivewire::class)->name('application-review');
    Route::get('/committee-voting', CommitteeVotingLivewire::class)->name('committee-voting');

    Route::get('/application-queue', ApplicationQueueLivewire::class)->name('application-queue');
    Route::get('/recommendation-review', RecommendationReviewLivewire::class)->name('recommendation-review');
    Route::get('/kpi-dashboard', KpiDashboardLivewire::class)->name('kpi-dashboard');
    Route::get('/users', UserLive::class)->name('admin.users');

    // Notifications are accessible by all roles
    Route::get('/notifications', NotificationsLivewire::class)->name('notifications');

    // Document Download (accessible by all who can view applications)
    Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

    // Reports
    Route::get('/reports', ReportLive::class)->name('reports');
});

