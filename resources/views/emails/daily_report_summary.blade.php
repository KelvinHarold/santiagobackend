<h2>Today's Sales Summary</h2>
<h2>Match:{{ $report->match_name }}</h2>
<p>Date: {{ $report->report_date->format('Y-m-d') }}</p>

<h3>Attendance</h3>
<ul>
    <li>Main Hall People: {{ $report->attendances->main_hall_people }}</li>
    <li>Main Hall Staff: {{ $report->attendances->main_hall_staff }}</li>
    <li>VIP Hall People: {{ $report->attendances->vip_hall_people }}</li>
    <li>VIP Hall Staff: {{ $report->attendances->vip_hall_staff }}</li>
</ul>

<h3>Expenses</h3>
<ul>
@foreach($report->expenses as $exp)
    <li>{{ $exp->title }}: {{ number_format($exp->amount, 2) }}</li>
@endforeach
</ul>
<p><strong>Total Expenses:</strong> {{ number_format($report->expenses->sum('amount'), 2) }}</p>
<p><strong>Total Income:</strong> {{ number_format($report->total_income, 2) }}</p>
<p><strong>Remaining Balance:</strong> {{ number_format($report->remaining_balance, 2) }}</p>

<h3>Revenue Shares</h3>
<ul>
@foreach($report->revenueShares as $share)
    <li>{{ $share->user->name }} ({{ $share->percentage }}%): {{ number_format($share->amount, 2) }}</li>
@endforeach
</ul>
