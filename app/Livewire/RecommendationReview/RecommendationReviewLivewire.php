<?php

namespace App\Livewire\RecommendationReview;

use App\Models\Recommendation;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecommendationReviewLivewire extends Component
{
    use LivewireAlert;

    public $recommendations;

    public function mount()
    {
        $this->loadRecommendations();
    }

    public function loadRecommendations()
    {
        $this->recommendations = Recommendation::where('status', 'pending_chairperson_review')
                                            ->with(['application.committeeVotes.member', 'application.user'])
                                            ->get();
    }

    public function approveRecommendation(Recommendation $recommendation)
    {
        $this->processChairpersonDecision($recommendation, 'approved', 'Application approved by Chairperson.');
    }

    public function rejectRecommendation(Recommendation $recommendation)
    {
        $this->processChairpersonDecision($recommendation, 'rejected', 'Application rejected by Chairperson.');
    }

    public function requestRevisionRecommendation(Recommendation $recommendation)
    {
        $this->processChairpersonDecision($recommendation, 'revision_requested', 'Application revision requested by Chairperson.');
    }

    protected function processChairpersonDecision(Recommendation $recommendation, $status, $description)
    {
        $recommendation->update(['status' => $status]);
        $recommendation->application->update(['status' => $status]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Chairperson decision',
            'description' => $description . ' for application ' . $recommendation->application->id,
            'auditable_type' => Application::class,
            'auditable_id' => $recommendation->application->id,
        ]);

        $this->sendNotifications($recommendation->application, $status);
        $this->alert('success', 'Decision recorded successfully.');
        $this->loadRecommendations();
    }

    protected function sendNotifications(Application $application, $status)
    {
        $usersToNotify = collect();

        // Add the applicant
        $usersToNotify->push($application->user);

        // Add all clerks, officers, and jtc members
        $clerks = User::where('role', 'district_court_clerk')->get();
        $officers = User::where('role', 'training_officer')->get();
        $jtcMembers = User::where('role', 'jtc_member')->get();

        $usersToNotify = $usersToNotify->merge($clerks)->merge($officers)->merge($jtcMembers)->unique('id');

        foreach ($usersToNotify as $user) {
            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\FinalDecisionMade',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => [
                    'application_id' => $application->id,
                    'message' => 'The final decision on application #' . $application->id . ' is: ' . $status,
                    'url' => route('application-review', ['id' => $application->id]),
                ],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function downloadReport(Recommendation $recommendation)
    {
        // This is a placeholder. In a real scenario, you would generate a PDF or other report.
        // For now, we'll create a dummy file and offer it for download.
        $reportContent = "Recommendation Report for Application ID: " . $recommendation->application->id . "\n\n";
        $reportContent .= "Recommendation: " . $recommendation->recommendation_text . "\n\n";
        $reportContent .= "Voting History:\n";

        foreach ($recommendation->application->committeeVotes as $vote) {
            $reportContent .= "- Member: " . ($vote->member->name ?? 'N/A') . ", Vote: " . $vote->vote . ", Comment: " . ($vote->comment ?? 'N/A') . "\n";
        }

        $fileName = 'recommendation_report_' . $recommendation->application->id . '.txt';
        Storage::disk('public')->put('reports/' . $fileName, $reportContent);

        return response()->download(storage_path('app/public/reports/' . $fileName));
    }

    public function render()
    {
        return view('livewire.recommendation-review.recommendation-review-livewire');
    }
}
