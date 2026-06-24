<?php

namespace App\Mail;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReportSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $report; // report data

    public function __construct(DailyReport $report)
    {
        $this->report = $report->load(['attendances', 'expenses', 'revenueShares.user']);
    }

    public function build()
    {
        return $this->subject("Daily Report Summary: {$this->report->match_name}")
                    ->view('emails.daily_report_summary');
    }
}
