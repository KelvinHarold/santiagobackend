<!DOCTYPE html>
<html>
<head>
    <title>Daily Report - {{ $report->match_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .section-title { background: #f0f0f0; padding: 8px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Match Report</h1>
        <h2>{{ $report->match_name }}</h2>
        <p>Date: {{ date('F d, Y', strtotime($report->report_date)) }}</p>
        <p>Uploaded By: <strong>{{ $report->uploader?->name ?? 'Unknown' }}</strong></p>
    </div>

    <div class="section">
        <div class="section-title">Income Summary</div>
        <table>
            <tr>
                <th>Total Income</th>
                <td>Tsh {{ number_format($report->total_income, 2) }}</td>
            </tr>
            <tr>
                <th>Total Expenses</th>
                <td>Tsh {{ number_format($report->expenses->sum('amount'), 2) }}</td>
            </tr>
            <tr>
                <th>Remaining Balance</th>
                <td>Tsh {{ number_format($report->remaining_balance, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Attendance</div>
        <table>
            <tr>
                <th>Main Hall People</th>
                <td>{{ $report->attendances->main_hall_people ?? 0 }}</td>
            </tr>
            <tr>
                <th>Main Hall Staff</th>
                <td>{{ $report->attendances->main_hall_staff ?? 0 }}</td>
            </tr>
            <tr>
                <th>VIP Hall People</th>
                <td>{{ $report->attendances->vip_hall_people ?? 0 }}</td>
            </tr>
            <tr>
                <th>VIP Hall Staff</th>
                <td>{{ $report->attendances->vip_hall_staff ?? 0 }}</td>
            </tr>
        </table>
    </div>

    @if($report->expenses->count() > 0)
    <div class="section">
        <div class="section-title">Expenses</div>
        <table>
            <thead>
                <tr><th>Title</th><th>Amount (Tsh)</th></tr>
            </thead>
            <tbody>
                @foreach($report->expenses as $expense)
                <tr>
                    <td>{{ $expense->title }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($report->revenueShares->count() > 0)
    <div class="section">
        <div class="section-title">Revenue Share Distribution</div>
        <table>
            <thead>
                <tr><th>User</th><th>Percentage</th><th>Amount (Tsh)</th></tr>
            </thead>
            <tbody>
                @foreach($report->revenueShares as $share)
                <tr>
                    <td>{{ $share->user->name ?? 'Unknown' }}</td>
                    <td>{{ $share->percentage }}%</td>
                    <td>{{ number_format($share->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>