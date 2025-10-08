<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $announcements = Announcements::count();
        $user = Auth::user();
        $customer_name = $user->name;

        // Get the latest 12 months of readings
        $readings = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'desc')
            ->limit(12)
            ->get();

        // Calculate monthly usage stats
        $monthlyUsage = $readings->first()->reading ?? 0;
        $currentBill = $readings->first()->amount ?? 0;

        // Calculate yearly consumption
        $yearlyConsumption = $readings->sum('consumption');

        // Get area average
        $areaAverage = $this->calculateAreaAverage($user);

        // Prepare billing data with proper status calculation
        $billingData = $readings->map(function ($reading) {
            $dueDate = $reading->due_date ? Carbon::parse($reading->due_date) : null;
            $today = now();

            // Determine status based on actual data
            $status = 'Pending';

            if ($reading->status === 'Paid') {
                $status = 'Paid';
            } elseif ($dueDate && $today->greaterThan($dueDate)) {
                $status = 'Overdue';
            } elseif ($dueDate && $today->diffInDays($dueDate, false) <= 5) {
                $status = 'Due Soon';
            }

            return [
                'month' => $reading->billing_month . ' ' . $reading->reading_date->format('Y'),
                'amount' => $reading->amount,
                'status' => $status,
                'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
                'is_current' => $reading->reading_date->format('Y-m') === now()->format('Y-m'),
            ];
        });

        // Prepare chart data
        $chartData = $readings->map(function ($reading) {
            return [
                'month' => $reading->billing_month,
                'consumption' => $reading->consumption,
                'reading' => $reading->reading,
            ];
        })->reverse()->values();

        return Inertia::render('Customer/Dashboard', [
            'customerName' => $customer_name,
            'announcements' => $announcements,
            'monthlyUsage' => $monthlyUsage,
            'currentBill' => $currentBill,
            'yearlyConsumption' => $yearlyConsumption,
            'areaAverage' => $areaAverage,
            'chartData' => $chartData,
            'billingData' => $billingData, // Add this line
        ]);
    }

    protected function calculateAreaAverage(User $user)
    {
        return MeterReading::whereHas('user', function ($query) use ($user) {
            $query->where('barangay', $user->barangay)
                ->where('municipality', $user->municipality);
        })
            ->where('reading_date', '>=', now()->subYear())
            ->avg('consumption') ?? 0;
    }
}
