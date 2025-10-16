<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Dashboard');
    }

    public function getDashboardData()
    {
        try {
            // Get date ranges
            $today = Carbon::today();
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Previous periods for comparison
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $staffUser = Auth::user();
            $staffName = $staffUser ? $staffUser->name : 'Staff';

            // Today's readings count
            $todaysReadings = MeterReading::whereDate('reading_date', $today)->count();

            // Weekly readings count
            $weeklyReadings = MeterReading::whereBetween('reading_date', [$startOfWeek, $endOfWeek])->count();
            $lastWeekReadings = MeterReading::whereBetween('reading_date', [$lastWeekStart, $lastWeekEnd])->count();

            // Monthly readings count
            $monthlyReadings = MeterReading::whereBetween('reading_date', [$startOfMonth, $endOfMonth])->count();
            $lastMonthReadings = MeterReading::whereBetween('reading_date', [$lastMonthStart, $lastMonthEnd])->count();

            // Calculate trends
            $weeklyTrend = $lastWeekReadings > 0 ?
                round((($weeklyReadings - $lastWeekReadings) / $lastWeekReadings) * 100, 1) : 0;

            $monthlyTrend = $lastMonthReadings > 0 ?
                round((($monthlyReadings - $lastMonthReadings) / $lastMonthReadings) * 100, 1) : 0;

            // Weekly chart data (last 7 days)
            $weeklyChartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dayReadings = MeterReading::whereDate('reading_date', $date)->count();

                // Calculate percentage for chart (max based on daily target)
                $dailyTarget = 50; // You can make this configurable
                $percentage = $dailyTarget > 0 ? min(($dayReadings / $dailyTarget) * 100, 100) : 0;

                $weeklyChartData[] = [
                    'day' => $date->format('D'),
                    'readings' => $dayReadings,
                    'percentage' => $percentage,
                    'date' => $date->format('Y-m-d')
                ];
            }

            // Recent activity (last 5 readings)
            $recentActivity = MeterReading::with(['user' => function ($query) {
                $query->select('id', 'name', 'lastname', 'account_number', 'serial_number', 'avatar');
            }])
                ->orderBy('reading_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->id,
                        'account_number' => $reading->user->account_number ?? 'N/A',
                        'customer_name' => ($reading->user->name ?? 'Unknown') . ' ' . ($reading->user->lastname ?? ''),
                        'serial_number' => $reading->user->serial_number ?? 'N/A',
                        'reading' => $reading->reading,
                        'consumption' => $reading->consumption,
                        'reading_date' => $reading->reading_date->format('Y-m-d'),
                        'reading_time' => $reading->created_at->format('H:i A'),
                        'status' => $reading->status,
                        'is_high_consumption' => $reading->consumption > 20,
                        'type' => 'reading',
                        'customer_avatar' => $reading->user->avatar ? Storage::url($reading->user->avatar) : null
                    ];
                });

            // Total customers and completion rate
            $totalCustomers = User::where('role', 'customer')->where('enabled', true)->count();
            $completedToday = MeterReading::whereDate('reading_date', $today)->count();
            $completionRate = $totalCustomers > 0 ? round(($completedToday / $totalCustomers) * 100, 1) : 0;

            // Pending readings
            $customersWithReadingsToday = MeterReading::whereDate('reading_date', $today)
                ->pluck('user_id')
                ->unique()
                ->toArray();

            $pendingCount = User::where('role', 'customer')
                ->where('enabled', true)
                ->whereNotIn('id', $customersWithReadingsToday)
                ->count();

            // Status counts for current month
            $statusCounts = [
                'paid' => MeterReading::where('status', 'Paid')
                    ->whereBetween('reading_date', [$startOfMonth, $endOfMonth])
                    ->count(),
                'pending' => MeterReading::where('status', 'Pending')
                    ->whereBetween('reading_date', [$startOfMonth, $endOfMonth])
                    ->count(),
                'overdue' => MeterReading::where('status', 'Overdue')
                    ->whereBetween('reading_date', [$startOfMonth, $endOfMonth])
                    ->count(),
            ];

            // Average reading time (mock data - you can implement actual timing logic)
            $avgReadingTime = $this->calculateAverageReadingTime();

            // Efficiency score (mock calculation based on various factors)
            $efficiencyScore = $this->calculateEfficiencyScore($todaysReadings, $completionRate, $pendingCount);

            return response()->json([
                'staffName' => $staffName,
                'todaysReadings' => $todaysReadings,
                'weeklyReadings' => $weeklyReadings,
                'monthlyReadings' => $monthlyReadings,
                'weeklyTrend' => $weeklyTrend,
                'monthlyTrend' => $monthlyTrend,
                'weeklyChartData' => $weeklyChartData,
                'recentActivity' => $recentActivity,
                'completedToday' => $completedToday,
                'pendingCount' => $pendingCount,
                'statusCounts' => $statusCounts,
                'dailyTarget' => 50, // Configurable target
                'completionRate' => $completionRate,
                'avgReadingTime' => $avgReadingTime,
                'efficiencyScore' => $efficiencyScore,
                'totalCustomers' => $totalCustomers
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());

            return response()->json([
                'todaysReadings' => 0,
                'weeklyReadings' => 0,
                'monthlyReadings' => 0,
                'weeklyTrend' => 0,
                'monthlyTrend' => 0,
                'weeklyChartData' => [],
                'recentActivity' => [],
                'completedToday' => 0,
                'pendingCount' => 0,
                'statusCounts' => [
                    'paid' => 0,
                    'pending' => 0,
                    'overdue' => 0,
                ],
                'dailyTarget' => 50,
                'completionRate' => 0,
                'avgReadingTime' => '0m',
                'efficiencyScore' => 0,
                'error' => 'Failed to load dashboard data'
            ], 500);
        }
    }

    /**
     * Calculate average reading time (mock implementation)
     */
    private function calculateAverageReadingTime()
    {
        // This is a mock implementation
        // In a real application, you would track actual reading times
        $averageMinutes = rand(2, 8);
        return $averageMinutes . 'm';
    }

    /**
     * Calculate efficiency score based on various metrics
     */
    private function calculateEfficiencyScore($todaysReadings, $completionRate, $pendingCount)
    {
        // Mock efficiency calculation
        $readingsScore = min($todaysReadings / 50 * 5, 5); // Max 5 points for readings
        $completionScore = min($completionRate / 20, 3); // Max 3 points for completion
        $pendingScore = max(2 - ($pendingCount / 10), 0); // Max 2 points for low pending

        $totalScore = $readingsScore + $completionScore + $pendingScore;

        return min(round($totalScore), 10); // Score out of 10
    }
}
