<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Report;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $currentYear;
    protected $currentMonth;

    public function __construct()
    {
        $this->currentYear = Carbon::now()->year;
        $this->currentMonth = Carbon::now()->month;
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData());
    }

    /**
     * Get comprehensive dashboard data.
     */
    protected function getDashboardData(): array
    {
        return [
            'total_users' => $this->getTotalUsers(),
            'total_staffs' => $this->getTotalStaffs(),
            'total_reports' => $this->getTotalReports(),
            'total_customers' => $this->getTotalCustomers(),
            'monthly_consumption' => $this->getMonthlyConsumption(),
            'current_month_consumption' => $this->getCurrentMonthConsumption(),
            'average_consumption' => $this->getAverageConsumption(),
            'peak_usage' => $this->getPeakUsage(),
            'growth_percentage' => $this->getGrowthPercentage(),
            'resolution_rate' => $this->getReportResolutionRate(),
            'recent_activities' => $this->getRecentActivities(),
            'recent_customers' => $this->getRecentCustomers(),
            'report_stats' => $this->getReportStats(),
            'consumption_by_area' => $this->getConsumptionByArea(),
            'recent_reports' => $this->getRecentReports(),
        ];
    }

    /**
     * Get total users count.
     */
    protected function getTotalUsers(): int
    {
        return User::count();
    }

    /**
     * Get total staff count.
     */
    protected function getTotalStaffs(): int
    {
        return User::role('staff')->count();
    }

    /**
     * Get total reports count.
     */
    protected function getTotalReports(): int
    {
        return Report::count();
    }

    /**
     * Get total customers count.
     */
    protected function getTotalCustomers(): int
    {
        return User::role('customer')->count();
    }

    /**
     * Get recent customers (last 5).
     */
    protected function getRecentCustomers(): array
    {
        return User::role('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'joined' => $user->created_at->diffForHumans(),
                    'created_at' => $user->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get report statistics by status.
     */
    protected function getReportStats(): array
    {
        return [
            'pending' => $this->getReportsByStatus('pending'),
            'in_progress' => $this->getReportsByStatus('in_progress'),
            'resolved' => $this->getReportsByStatus('resolved'),
            'total' => $this->getTotalValidReports(),
        ];
    }

    /**
     * Get reports count by status.
     */
    protected function getReportsByStatus(string $status): int
    {
        return Report::where('status', $status)
            ->whereNull('deleted_at')
            ->where('is_merged_reference', false)
            ->count();
    }

    /**
     * Get total valid reports count.
     */
    protected function getTotalValidReports(): int
    {
        return Report::whereNull('deleted_at')
            ->where('is_merged_reference', false)
            ->count();
    }

    /**
     * Get consumption by area (zones).
     */
    protected function getConsumptionByArea(): array
    {
        $zones = $this->getZones();
        $consumptionByZone = [];

        foreach ($zones as $zone) {
            $reportCount = Report::where('zone', $zone)
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->count();

            if ($reportCount > 0) {
                $consumptionByZone[] = [
                    'name' => $zone,
                    'consumption' => $reportCount,
                ];
            }
        }

        return $this->sortAndLimit($consumptionByZone, 'consumption', 5);
    }

    /**
     * Get available zones.
     */
    protected function getZones(): array
    {
        return [
            'Zone 1',
            'Zone 2',
            'Zone 3',
            'Zone 4',
            'Zone 5',
            'Zone 6',
            'Zone 7',
            'Zone 8',
            'Zone 9',
            'Zone 10',
            'Zone 11',
            'Zone 12'
        ];
    }

    /**
     * Sort array by key and limit results.
     */
    protected function sortAndLimit(array $data, string $sortKey, int $limit): array
    {
        usort($data, function ($a, $b) use ($sortKey) {
            return $b[$sortKey] - $a[$sortKey];
        });

        return array_slice($data, 0, $limit);
    }

    /**
     * Get recent reports for dashboard.
     */
    protected function getRecentReports(): array
    {
        return Report::with(['user'])
            ->whereNull('deleted_at')
            ->where('is_merged_reference', false)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'tracking_code' => $report->tracking_code,
                    'customer' => $report->user ? $report->user->name : $report->reporter_name,
                    'type' => $report->water_issue_type,
                    'status' => $report->status,
                    'priority' => $report->priority,
                    'date' => $report->created_at->toISOString(),
                    'zone' => $report->zone,
                ];
            })
            ->toArray();
    }

    /**
     * Get monthly consumption data.
     */
    protected function getMonthlyConsumption(): array
    {
        $consumptionData = [];

        for ($month = 1; $month <= 12; $month++) {
            $consumptionData[] = $this->getMonthlyConsumptionForMonth($month);
        }

        return $consumptionData;
    }

    /**
     * Get consumption for specific month.
     */
    protected function getMonthlyConsumptionForMonth(int $month): float
    {
        $startDate = Carbon::create($this->currentYear, $month, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $month, 1)->endOfMonth();

        $totalConsumption = MeterReading::whereBetween('reading_date', [$startDate, $endDate])
            ->sum('consumption');

        return round($totalConsumption, 2);
    }

    /**
     * Get current month's consumption.
     */
    protected function getCurrentMonthConsumption(): float
    {
        return $this->getMonthlyConsumptionForMonth($this->currentMonth);
    }

    /**
     * Get average consumption for the year.
     */
    protected function getAverageConsumption(): float
    {
        $startDate = Carbon::create($this->currentYear, 1, 1)->startOfYear();
        $endDate = Carbon::create($this->currentYear, 12, 31)->endOfYear();

        $totalConsumption = MeterReading::whereBetween('reading_date', [$startDate, $endDate])
            ->sum('consumption');

        $monthCount = $this->currentMonth;

        return $monthCount > 0 ? round($totalConsumption / $monthCount, 2) : 0;
    }

    /**
     * Get peak usage for the year.
     */
    protected function getPeakUsage(): float
    {
        $peakUsage = 0;

        for ($month = 1; $month <= 12; $month++) {
            $monthlyConsumption = $this->getMonthlyConsumptionForMonth($month);
            $peakUsage = max($peakUsage, $monthlyConsumption);
        }

        return $peakUsage;
    }

    /**
     * Get growth percentage compared to previous month.
     */
    protected function getGrowthPercentage(): float
    {
        if ($this->currentMonth == 1) {
            return 0;
        }

        $currentConsumption = $this->getCurrentMonthConsumption();
        $previousConsumption = $this->getMonthlyConsumptionForMonth($this->currentMonth - 1);

        if ($previousConsumption == 0) {
            return $currentConsumption > 0 ? 100 : 0;
        }

        return round((($currentConsumption - $previousConsumption) / $previousConsumption) * 100, 1);
    }

    /**
     * Get report resolution rate.
     */
    protected function getReportResolutionRate(): float
    {
        $totalReports = $this->getTotalValidReports();
        $resolvedReports = $this->getReportsByStatus('resolved');

        return $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100, 0) : 0;
    }

    /**
     * Get recent activities with formatted data.
     */
    protected function getRecentActivities(): array
    {
        return Activity::latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'event' => $activity->event,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'properties' => $activity->properties,
                    'subject_type' => $activity->subject_type,
                ];
            })
            ->toArray();
    }

    /**
     * Export water analytics data as CSV.
     */
    public function exportWaterAnalytics(Request $request)
    {
        $data = $request->validate([
            'monthly_consumption' => 'required|array',
            'current_month_consumption' => 'required|numeric',
            'average_consumption' => 'required|numeric',
            'peak_usage' => 'required|numeric',
        ]);

        $filename = "water_analytics_" . Carbon::now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($data) {
            $this->generateCsv($data);
        }, 200, $headers);
    }

    /**
     * Generate CSV file.
     */
    protected function generateCsv(array $data): void
    {
        $file = fopen('php://output', 'w');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Write headers
        fputcsv($file, [
            'Month',
            'Monthly Consumption (m³)',
            'Current Month Consumption (m³)',
            'Average Consumption (m³)',
            'Peak Usage (m³)',
        ]);

        // Write data
        foreach ($data['monthly_consumption'] as $index => $consumption) {
            fputcsv($file, [
                $months[$index],
                round($consumption, 2),
                round($data['current_month_consumption'], 2),
                round($data['average_consumption'], 2),
                round($data['peak_usage'], 2),
            ]);
        }

        fclose($file);
    }
}
