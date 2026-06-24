<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\RevenueShare;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Mail\DailyReportSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DailyReportController extends Controller
{
    /**
     * Fetch paginated reports with relationships
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);

        $reports = DailyReport::with([
            'attendances',
            'expenses',
            'revenueShares.user:id,name',
            'uploader:id,name',
        ])
            ->orderBy('report_date', 'desc')
            ->paginate($perPage);

        // Transform to camelCase
        $transformedReports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'match_name' => $report->match_name,
                'report_date' => $report->report_date,
                'total_income' => $report->total_income,
                'remaining_balance' => $report->remaining_balance,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'uploaded_by_name' => $report->uploader?->name ?? 'Unknown',
                'attendances' => $report->attendances,
                'expenses' => $report->expenses,
                'revenueShares' => $report->revenueShares,
            ];
        });

        return response()->json([
            'current_page' => $reports->currentPage(),
            'data' => $transformedReports,
            'last_page' => $reports->lastPage(),
            'per_page' => $reports->perPage(),
            'total' => $reports->total(),
        ]);
    }
    /**
     * Store new daily report
     */
    public function store(Request $request)
    {
        $request->validate([
            'match_name' => 'required|string',
            'total_income' => 'required|numeric',
            'attendances' => 'required|array',
            'expenses' => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Save main record
            $report = DailyReport::create([
                'match_name' => $request->match_name,
                'report_date' => now(),
                'total_income' => $request->total_income,
                'uploaded_by' => auth()->id(),
            ]);

            // 2️⃣ Save attendance
            Attendance::create([
                'daily_report_id' => $report->id,
                'main_hall_people' => $request->attendances['main_hall_people'] ?? 0,
                'main_hall_staff' => $request->attendances['main_hall_staff'] ?? 0,
                'vip_hall_people' => $request->attendances['vip_hall_people'] ?? 0,
                'vip_hall_staff' => $request->attendances['vip_hall_staff'] ?? 0,
            ]);

            // 3️⃣ Save expenses
            $totalExpenses = 0;
            if ($request->has('expenses')) {
                foreach ($request->expenses as $exp) {
                    Expense::create([
                        'daily_report_id' => $report->id,
                        'title' => $exp['title'],
                        'amount' => $exp['amount']
                    ]);
                    $totalExpenses += $exp['amount'];
                }
            }

            // 4️⃣ Compute remaining balance
            $remaining = $report->total_income - $totalExpenses;
            $report->update(['remaining_balance' => $remaining]);

            // 5️⃣ Mgao wa mapato kwa kila user
            $users = User::where('revenue_percentage', '>', 0)->get();

            foreach ($users as $user) {
                $amount = ($remaining * $user->revenue_percentage) / 100;

                RevenueShare::create([
                    'daily_report_id' => $report->id,
                    'user_id' => $user->id,
                    'percentage' => $user->revenue_percentage,
                    'amount' => $amount,
                ]);
            }

            // 6️⃣ Tuma email kwa Admin
            $adminEmail = env('ADMIN_REPORT_EMAIL', config('mail.from.address'));
            Mail::to($adminEmail)->send(new DailyReportSummary($report->load(['attendances', 'expenses', 'revenueShares.user'])));

            // 7️⃣ Tengeneza Notification kwa Admins
            $admins = User::where('role', 'Admin')->get();
            $uploaderName = auth()->user()?->name ?? 'Msaidizi';
            foreach ($admins as $admin) {
                if ($admin->id !== auth()->id()) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'Ripoti Mpya Imewasilishwa',
                        'message' => "Ripoti mpya ya mechi '{$report->match_name}' imewasilishwa na {$uploaderName}.",
                        'report_id' => $report->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Daily report recorded successfully and email sent!',
                'report' => $report->load(['attendances', 'expenses', 'revenueShares.user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log full error for debugging without exposing it to users
            \Illuminate\Support\Facades\Log::error('DailyReport store failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Failed to record report. Please try again or contact support.',
            ], 500);
        }
    }

    /**
     * Get users with revenue percentages
     */
    public function usersWithPercentage()
    {
        $users = User::where('revenue_percentage', '>', 0)
            ->select('id', 'name', 'revenue_percentage')
            ->get();

        return response()->json($users);
    }

    /**
     * Daily total expenses summary
     */
    public function dailyExpenses()
    {
        $daily = Expense::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as amount')
        )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $total_expenses = $daily->sum('amount');

        return response()->json([
            'total_expenses' => $total_expenses,
            'daily' => $daily
        ]);
    }

    /**
     * Office daily revenue share summary
     */
    public function officeDaily()
    {
        $officeUser = User::where('role', 'Office')->first();

        if (!$officeUser) {
            return response()->json([
                'total_office_amount' => 0,
                'daily' => []
            ]);
        }

        $daily = RevenueShare::where('user_id', $officeUser->id)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $total_office_amount = $daily->sum('amount');

        return response()->json([
            'total_office_amount' => $total_office_amount,
            'daily' => $daily
        ]);
    }


    public function destroy($id)
    {
        try {
            $report = DailyReport::findOrFail($id);

            // Futa records zote zinazohusiana
            $report->attendances()->delete();
            $report->expenses()->delete();
            $report->revenueShares()->delete();

            // Futa report yenyewe
            $report->delete();

            return response()->json(['message' => 'Report deleted successfully.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DailyReport destroy failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete report.',
            ], 500);
        }
    }


    /**
     * Download report as PDF
     */
    public function download($id)
    {
        try {
            $report = DailyReport::with([
                'attendances',
                'expenses',
                'revenueShares.user:id,name',
                'uploader:id,name',
            ])->findOrFail($id);

            // Load PDF library (you need to install a package like barryvdh/laravel-dompdf)
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.daily-report', compact('report'));

            return $pdf->download("report_{$report->match_name}_{$report->report_date}.pdf");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DailyReport download failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to generate PDF.',
            ], 500);
        }
    }
}
