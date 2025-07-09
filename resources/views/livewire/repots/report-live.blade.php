<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12 col-lg-12">
                    <div class="d-flex justify-content-end py-2">
                        <a href="{{ route('reports.pdf') }}" class="btn btn-dark">Download PDF</a>
                    </div>
                </div>
                <!-- Purchase Requests Status Chart -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Purchase Requests by Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="prStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bids Status Chart -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Bids by Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="bidStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Inventory Stock Levels Chart -->
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Inventory Stock Levels</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="stockLevelChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Helper to init a pie chart
            function initPieChart(ctxId, labels, data, bgColors) {
                const ctx = document.getElementById(ctxId).getContext('2d');
                return new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: bgColors
                        }]
                    }
                });
            }

            // PR Status Chart
            initPieChart(
                'prStatusChart',
                {!! json_encode(array_keys($prStatusCounts)) !!},
                {!! json_encode(array_values($prStatusCounts)) !!},
                ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8']
            );

            // Bid Status Chart
            initPieChart(
                'bidStatusChart',
                {!! json_encode(array_keys($bidStatusCounts)) !!},
                {!! json_encode(array_values($bidStatusCounts)) !!},
                ['#6f42c1', '#e83e8c', '#20c997', '#fd7e14']
            );

            // Stock Level Chart
            initPieChart(
                'stockLevelChart',
                {!! json_encode(array_keys($stockLevels)) !!},
                {!! json_encode(array_values($stockLevels)) !!},
                ['#dc3545', '#28a745']
            );
        </script>
    @endpush
</div>
