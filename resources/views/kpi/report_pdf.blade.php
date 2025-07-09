<!DOCTYPE html>
<html>
<head>
    <title>KPI Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { color: #333; }
    </style>
</head>
<body>
    <h1>KPI Report</h1>
    <p>Report generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    <p>Period: {{ $period }} ({{ $startDate }} to {{ $endDate }})</p>

    <h2>Average Processing Time: {{ $averageProcessingTime }} Days</h2>
    <h2>Backlog Volume: {{ $backlogVolume }}</h2>

    <h2>Applications Submitted Per Period</h2>
    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicationsSubmittedPerMonth['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $applicationsSubmittedPerMonth['data'][$index] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Workload Distribution Per Reviewer</h2>
    <table>
        <thead>
            <tr>
                <th>Reviewer</th>
                <th>Pending Reviews</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workloadDistribution['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $workloadDistribution['data'][$index] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Approval vs. Rejection Trends</h2>
    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Approved</th>
                <th>Rejected</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvalRejectionTrends['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $approvalRejectionTrends['approved'][$index] }}</td>
                    <td>{{ $approvalRejectionTrends['rejected'][$index] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

  </body>
</html>
