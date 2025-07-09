<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 4px; }
        th { background: #f0f0f0; }
        h1, h2, h3 { margin: 0 0 10px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    @switch($reportType)
        @case('overview')
            <table>
                <thead><tr><th>Entity</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($data as $entity => $count)
                        <tr>
                            <td>{{ ucwords(str_replace('_',' ', $entity)) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('purchase_requests')
            <p><strong>Total Cost:</strong> {{ number_format($data['total_cost'],2) }} &nbsp;
               <strong>Approved:</strong> {{ $data['approved'] }} &nbsp;
               <strong>Rejected:</strong> {{ $data['rejected'] }}</p>
            <table>
                <thead><tr><th>ID</th><th>Requester</th><th>Dept</th><th>Cost</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($data['records'] as $pr)
                        <tr>
                            <td>{{ $pr->id }}</td>
                            <td>{{ $pr->user->name }}</td>
                            <td>{{ $pr->department->name }}</td>
                            <td>{{ number_format($pr->estimated_cost,2) }}</td>
                            <td>{{ ucfirst($pr->status) }}</td>
                            <td>{{ $pr->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('tenders')
            <p><strong>Total Bids Amount:</strong> {{ number_format($data['total_bids_amount'],2) }} &nbsp;
               <strong>Accepted:</strong> {{ $data['accepted_bids'] }} &nbsp;
               <strong>Rejected:</strong> {{ $data['rejected_bids'] }}</p>
            <table>
                <thead><tr><th>ID</th><th>Title</th><th>Deadline</th><th>Bids</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($data['records'] as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->title }}</td>
                            <td>{{ $t->deadline->format('Y-m-d') }}</td>
                            <td>{{ $t->bids_count }}</td>
                            <td>{{ ucfirst($t->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('inventory')
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Stock</th><th>Min Stock</th><th>Unit</th></tr></thead>
                <tbody>
                    @foreach($data['records'] as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->current_stock }}</td>
                            <td>{{ $item->minimum_stock }}</td>
                            <td>{{ $item->unit_of_measure }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('financial')
            <p><strong>Total Invoice Amount:</strong> {{ number_format($data['total_invoices'],2) }} &nbsp;
               <strong>Total Payments:</strong> {{ number_format($data['total_payments'],2) }} &nbsp;
               <strong>Paid:</strong> {{ $data['invoices_paid'] }} &nbsp;
               <strong>Pending:</strong> {{ $data['invoices_pending'] }}</p>
            <h2>Invoices</h2>
            <table>
                <thead><tr><th>No</th><th>Amount</th><th>Status</th><th>Due</th></tr></thead>
                <tbody>
                    @foreach($data['invoices'] as $inv)
                        <tr>
                            <td>{{ $inv->invoice_number }}</td>
                            <td>{{ number_format($inv->amount,2) }}</td>
                            <td>{{ ucfirst($inv->status) }}</td>
                            <td>{{ $inv->due_date->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h2>Payments</h2>
            <table>
                <thead><tr><th>ID</th><th>Invoice No</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($data['payments'] as $pay)
                        <tr>
                            <td>{{ $pay->id }}</td>
                            <td>{{ $pay->invoice->invoice_number }}</td>
                            <td>{{ number_format($pay->amount,2) }}</td>
                            <td>{{ $pay->payment_date->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

    @endswitch
</body>
</html>
