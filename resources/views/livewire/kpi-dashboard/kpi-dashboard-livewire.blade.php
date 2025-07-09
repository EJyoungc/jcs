<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>KPI Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">KPI Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="periodFilter">Period:</label>
                    <select wire:model.live="period" id="periodFilter" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="startDateFilter">Start Date:</label>
                    <input type="date" wire:model.live="startDate" id="startDateFilter" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="endDateFilter">End Date:</label>
                    <input type="date" wire:model.live="endDate" id="endDateFilter" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="exportPdf" class="btn btn-danger mr-2">Export PDF</button>
                    <button wire:click="exportCsv" class="btn btn-success">Export CSV</button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $averageProcessingTime }}<sup style="font-size: 20px"> Days</sup></h3>
                            <p>Avg. Processing Time</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $backlogVolume }}</h3>
                            <p>Backlog Volume</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cube"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Applications Submitted Per Period</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="applicationsSubmittedChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Workload Distribution Per Reviewer</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="workloadDistributionChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Approval vs. Rejection Trends</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="approvalRejectionTrendsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                renderCharts();
            });

            Livewire.on('kpiDataUpdated', () => {
                renderCharts();
            });

            function renderCharts() {
                // Applications Submitted Per Period Chart
                const appSubmittedCtx = document.getElementById('applicationsSubmittedChart').getContext('2d');
                if (window.applicationsSubmittedChart instanceof Chart) {
                    window.applicationsSubmittedChart.destroy();
                }
                window.applicationsSubmittedChart = new Chart(appSubmittedCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($applicationsSubmittedPerMonth['labels']),
                        datasets: [{
                            label: 'Applications',
                            data: @json($applicationsSubmittedPerMonth['data']),
                            backgroundColor: 'rgba(0, 123, 255, 0.5)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Workload Distribution Per Reviewer Chart
                const workloadCtx = document.getElementById('workloadDistributionChart').getContext('2d');
                if (window.workloadDistributionChart instanceof Chart) {
                    window.workloadDistributionChart.destroy();
                }
                window.workloadDistributionChart = new Chart(workloadCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($workloadDistribution['labels']),
                        datasets: [{
                            data: @json($workloadDistribution['data']),
                            backgroundColor: [
                                '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });

                // Approval vs. Rejection Trends Chart
                const approvalRejectionCtx = document.getElementById('approvalRejectionTrendsChart').getContext('2d');
                if (window.approvalRejectionTrendsChart instanceof Chart) {
                    window.approvalRejectionTrendsChart.destroy();
                }
                window.approvalRejectionTrendsChart = new Chart(approvalRejectionCtx, {
                    type: 'line',
                    data: {
                        labels: @json($approvalRejectionTrends['labels']),
                        datasets: [
                            {
                                label: 'Approved',
                                data: @json($approvalRejectionTrends['approved']),
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                fill: true,
                                tension: 0.1
                            },
                            {
                                label: 'Rejected',
                                data: @json($approvalRejectionTrends['rejected']),
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                                fill: true,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
</div>