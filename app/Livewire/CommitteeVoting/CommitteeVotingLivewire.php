<?php

namespace App\Livewire\CommitteeVoting;

use App\Models\CommitteeVote;
use App\Models\Application;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Recommendation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommitteeVotingLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;
    public $applications;
    public $selectedApplication;
    public $userVote = [];
    public $comment = [];
    public $votedMembersCount = [];
    public $totalJtcMembers = 0;

    protected $listeners = ['voteCast' => 'loadApplications'];

    public function mount()
    {
        $this->loadApplications();
        $this->totalJtcMembers = User::where('role', 'jtc_member')->count(); // Assuming 'jtc_member' is the role for JTC members
    }

    public function loadApplications()
    {
        $this->applications = Application::where('status', 'forwarded_to_jtc_committee')
                                        ->with(['committeeVotes.member'])
                                        ->get();

        foreach ($this->applications as $application) {
            $this->votedMembersCount[$application->id] = $application->committeeVotes->count();
            $this->userVote[$application->id] = $application->committeeVotes
                                                ->where('jtc_member_id', Auth::id())
                                                ->first()->vote ?? null;
            $this->comment[$application->id] = $application->committeeVotes
                                                ->where('jtc_member_id', Auth::id())
                                                ->first()->comment ?? null;
        }
    }

    public function castVote($applicationId, $voteType)
    {
        $application = Application::findOrFail($applicationId);

        // Check if the user has already voted for this application
        $existingVote = CommitteeVote::where('application_id', $applicationId)
                                    ->where('jtc_member_id', Auth::id())
                                    ->first();

        if ($existingVote) {
            $this->alert('warning', 'You have already voted for this application.');
            return;
        }

        $rules = [
            'comment.' . $applicationId => 'nullable|string',
        ];

        $this->validate($rules);

        CommitteeVote::create([
            'application_id' => $applicationId,
            'jtc_member_id' => Auth::id(),
            'vote' => $voteType,
            'comment' => $this->comment[$applicationId] ?? null,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Voted on application',
            'description' => 'User ' . Auth::user()->name . ' cast a ' . $voteType . ' vote for application ' . $applicationId,
            'auditable_type' => Application::class,
            'auditable_id' => $applicationId,
        ]);

        $this->alert('success', 'Vote cast successfully.');
        $this->dispatch('voteCast'); // Trigger reload of applications

        $this->checkVotingCompletion($application);
    }

    protected function checkVotingCompletion(Application $application)
    {
        $votedCount = $application->committeeVotes->count();
        if ($votedCount >= $this->totalJtcMembers) {
            $this->lockVoting($application);
            $this->calculateOutcome($application);
            $this->generateRecommendation($application);
            $this->sendNotificationToChairperson($application);
        }
    }

    protected function lockVoting(Application $application)
    {
        $application->update(['status' => 'voting_closed']); // Update application status
        AuditLog::create([
            'user_id' => Auth::id(), // Or a system user if this is an automated action
            'action' => 'Voting locked',
            'description' => 'Voting locked for application ' . $application->id . ' as all JTC members have voted.',
            'auditable_type' => Application::class,
            'auditable_id' => $application->id,
        ]);
    }

    protected function calculateOutcome(Application $application)
    {
        $votes = $application->committeeVotes;
        $approved = $votes->where('vote', 'Approve')->count();
        $rejected = $votes->where('vote', 'Reject')->count();
        $revision = $votes->where('vote', 'Request Revision')->count();

        $outcome = 'pending';
        if ($approved > $rejected && $approved > $revision) {
            $outcome = 'approved';
        } elseif ($rejected > $approved && $rejected > $revision) {
            $outcome = 'rejected';
        } else {
            $outcome = 'revision_requested';
        }

        $application->update(['voting_outcome' => $outcome]); // Assuming a 'voting_outcome' field in applications table
        AuditLog::create([
            'user_id' => Auth::id(), // Or a system user
            'action' => 'Voting outcome calculated',
            'description' => 'Voting outcome for application ' . $application->id . ' is: ' . $outcome,
            'auditable_type' => Application::class,
            'auditable_id' => $application->id,
        ]);
    }

    protected function generateRecommendation(Application $application)
    {
        $outcome = $application->voting_outcome;
        $recommendationText = "Based on the committee voting, the recommendation for application " . $application->id . " is to " . $outcome . ".";

        Recommendation::create([
            'application_id' => $application->id,
            'recommendation_text' => $recommendationText,
            'status' => 'pending_chairperson_review',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(), // Or a system user
            'action' => 'Recommendation generated',
            'description' => 'Recommendation generated for application ' . $application->id . ' with outcome: ' . $outcome,
            'auditable_type' => Application::class,
            'auditable_id' => $application->id,
        ]);
    }

    protected function sendNotificationToChairperson(Application $application)
    {
        $chairperson = User::where('role', 'chairperson')->first(); // Assuming 'chairperson' role
        if ($chairperson) {
            $chairperson->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\VotingCompleted',
                'notifiable_type' => User::class,
                'notifiable_id' => $chairperson->id,
                'data' => [
                    'application_id' => $application->id,
                    'message' => 'Voting has been completed for application #' . $application->id . '. It is ready for your review.',
                    'url' => route('recommendation-review', ['applicationId' => $application->id]),
                ],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.committee-voting.committee-voting-livewire');
    }
}
