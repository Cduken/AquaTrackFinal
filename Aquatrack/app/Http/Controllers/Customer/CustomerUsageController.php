<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class CustomerUsageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $readings = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($reading) {
                // Convert due_date string to Carbon date
                $dueDate = $reading->due_date ? Carbon::parse($reading->due_date) : null;

                return [
                    'id' => $reading->id,
                    'month' => $reading->billing_month . ' ' . ($reading->reading_date ? $reading->reading_date->format('Y') : date('Y')),
                    'usage' => $reading->consumption,
                    'amount' => number_format($reading->amount, 2),
                    'status' => $reading->status ?? ($reading->amount > 0 ? 'Pending' : 'Paid'),
                    'due_date' => $reading->due_date, // Keep as raw string
                    'due_date_formatted' => $dueDate ? $dueDate->format('M d, Y') : 'N/A',
                    'reading_date' => $reading->reading_date ? $reading->reading_date->format('Y-m-d') : null,
                ];
            });

        $chartData = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'asc')
            ->limit(6)
            ->get()
            ->map(function ($reading) {
                return [
                    'month' => $reading->billing_month,
                    'usage' => $reading->consumption,
                    'amount' => $reading->amount,
                ];
            });

        return Inertia::render('Customer/Usage', [
            'usageData' => $readings,
            'chartData' => $chartData,
        ]);
    }

    public function show($month)
    {
        $user = Auth::user();

        // URL decode the month parameter (it might contain spaces and special characters)
        $decodedMonth = urldecode($month);

        // Parse month and year from the parameter (format: "January 2024")
        $monthParts = explode(' ', $decodedMonth);
        $monthName = $monthParts[0];
        $year = $monthParts[1] ?? date('Y');

        // Get the meter reading for this month
        $reading = MeterReading::where('user_id', $user->id)
            ->where('billing_month', $monthName)
            ->whereYear('reading_date', $year)
            ->first();

        if (!$reading) {
            abort(404, 'Usage record not found');
        }

        // Get previous readings for context (last 12 months)
        $previousReadings = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($reading) {
                $dueDate = $reading->due_date ? Carbon::parse($reading->due_date) : null;

                return [
                    'billing_month' => $reading->billing_month,
                    'reading_date' => $reading->reading_date ? $reading->reading_date->format('Y-m-d') : 'N/A',
                    'previous_reading' => $reading->previous_reading,
                    'reading' => $reading->reading,
                    'consumption' => $reading->consumption,
                    'amount' => $reading->amount,
                    'year' => $reading->reading_date ? $reading->reading_date->format('Y') : date('Y'),
                    'due_date' => $reading->due_date,
                    'due_date_formatted' => $dueDate ? $dueDate->format('M d, Y') : 'N/A',
                    'status' => $reading->status ?? ($reading->amount > 0 ? 'Pending' : 'Paid'),
                ];
            });

        return Inertia::render('Customer/Usage/UsageDetails', [
            'usage' => [
                'month' => $decodedMonth,
                'usage' => $reading->consumption,
                'amount' => number_format($reading->amount, 2),
                'status' => $reading->status ?? ($reading->amount > 0 ? 'Pending' : 'Paid'),
                'due_date' => $reading->due_date,
                'due_date_formatted' => $reading->due_date ? Carbon::parse($reading->due_date)->format('M d, Y') : 'N/A',
                'previous_reading' => $reading->previous_reading,
                'current_reading' => $reading->reading,
                'reading_date' => $reading->reading_date ? $reading->reading_date->format('Y-m-d') : null,
            ],
            'previousReadings' => $previousReadings,
        ]);
    }

    // You can remove the getPreviousReadings API method since we're now loading everything in the show method
    // unless you need it for other purposes

    public function update(Request $request, $id)
    {
        $reading = MeterReading::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Paid'
        ]);

        $reading->status = $validated['status'];
        $reading->save();

        $dueDate = $reading->due_date ? Carbon::parse($reading->due_date) : null;

        return response()->json([
            'message' => 'Bill status updated successfully!',
            'reading' => [
                'id' => $reading->id,
                'month' => $reading->billing_month . ' ' . ($reading->reading_date ? $reading->reading_date->format('Y') : date('Y')),
                'usage' => $reading->consumption,
                'amount' => number_format($reading->amount, 2),
                'status' => $reading->status,
                'due_date' => $reading->due_date,
                'due_date_formatted' => $dueDate ? $dueDate->format('M d, Y') : 'N/A',
            ]
        ]);
    }
}
