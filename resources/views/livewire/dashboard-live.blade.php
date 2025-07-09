<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if ($userRole === 'candidate')
                <h3>My Applications</h3>
                @if ($dashboardData['applications']->isEmpty())
                    <p>You have not submitted any applications yet.</p>
                @else
                    <div class="row">
                        @foreach ($dashboardData['applications'] as $application)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Application ID: {{ $application->id }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Status:</strong> <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span></p>
                                        <p><strong>Submitted On:</strong> {{ $application->submitted_at->format('Y-m-d H:i') }}</p>
                                        <a href="/applications/{{ $application->id }}" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($userRole === 'district_court_clerk' || $userRole === 'training_officer' || $userRole === 'jtc_member')
                <h3>My Workload</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Pending Applications</h5>
                                    <h3>{{ $dashboardData['pending_applications_count'] ?? 0 }}</h3>
                                </div>
                                <a href="/applications" class="text-white">View All Applications <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Personal Review Workload</h5>
                                    <h3>{{ $dashboardData['personal_review_workload_count'] ?? 0 }}</h3>
                                </div>
                                <a href="/reviews" class="text-white">View My Reviews <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($userRole === 'chairperson')
                <h3>Recommendations Awaiting Approval</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Pending Recommendations</h5>
                                    <h3>{{ $dashboardData['pending_recommendations_count'] ?? 0 }}</h3>
                                </div>
                                <a href="/recommendation-review" class="text-white">Review Recommendations <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($userRole === 'system_administrator')
                <h3>System-Wide KPIs</h3>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Total Applications</h5>
                                    <h3>{{ $dashboardData['total_applications'] ?? 0 }}</h3>
                                </div>
                                <a href="/applications" class="text-white">View All Applications <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Pending Applications</h5>
                                    <h3>{{ $dashboardData['pending_applications'] ?? 0 }}</h3>
                                </div>
                                <a href="/applications?status=pending" class="text-white">View Pending <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Approved Applications</h5>
                                    <h3>{{ $dashboardData['approved_applications'] ?? 0 }}</h3>
                                </div>
                                <a href="/applications?status=approved" class="text-white">View Approved <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Rejected Applications</h5>
                                    <h3>{{ $dashboardData['rejected_applications'] ?? 0 }}</h3>
                                </div>
                                <a href="/applications?status=rejected" class="text-white">View Rejected <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Application Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="applicationStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Applications Over Time</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="applicationsOverTimeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Quick Links</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><a href="/users">User Management</a></li>
                                    <li class="list-group-item"><a href="/kpi-dashboard">KPI Dashboard</a></li>
                                    <li class="list-group-item"><a href="/audit-logs">Audit Logs</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Recent System Activities</h5>
                        </div>
                        <div class="card-body">
                            @if ($recentActivities->isEmpty())
                                <p>No recent activities.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($recentActivities as $activity)
                                        <li class="list-group-item">
                                            <strong>{{ $activity->action }}:</strong> {{ $activity->description }}
                                            <br><small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                @if ($userRole === 'system_administrator')
                    // Application Status Distribution Chart
                    const applicationStatusCtx = document.getElementById('applicationStatusChart').getContext('2d');
                    new Chart(applicationStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($applicationStatusChartData['labels']),
                            datasets: [{
                                data: @json($applicationStatusChartData['data']),
                                backgroundColor: @json($applicationStatusChartData['colors']),
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false,
                                    text: 'Application Status Distribution'
                                }
                            }
                        },
                    });

                    // Applications Over Time Chart
                    const applicationsOverTimeCtx = document.getElementById('applicationsOverTimeChart').getContext('2d');
                    new Chart(applicationsOverTimeCtx, {
                        type: 'line',
                        data: {
                            labels: @json($applicationsOverTimeChartData['labels']),
                            datasets: [{
                                label: 'Applications Submitted',
                                data: @json($applicationsOverTimeChartData['data']),
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                                fill: true,
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false,
                                    text: 'Applications Over Time'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                @endif
            });
        </script>
    @endpush
</div>