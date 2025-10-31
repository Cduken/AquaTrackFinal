<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRecordController extends Controller
{
    /**
     * Get the due date based on the user's zone and reading date.
     */
    private function getDueDate($user, $readingDate)
    {
        // Extract numeric zone, default to 1 if invalid
        $zone = $user->zone ? (int) preg_replace('/[^0-9]/', '', $user->zone) : 1;
        if ($zone < 1 || $zone > 12) {
            $zone = 1; // Fallback to Zone 1
        }

        // Define due dates strictly per zone rules
        $dueDayMap = [
            1 => 15, // Zone 1: 15th
            2 => 16, // Zone 2: 16th
            3 => 16, // Zone 3: 16th
            4 => 17, // Zone 4: 17th
            5 => 18, // Zone 5: 18th
            6 => 19, // Zone 6: 19th
            7 => 19, // Zone 7: 19th
            8 => 20, // Zone 8: 20th
            9 => 21, // Zone 9: 21st
            10 => 22, // Zone 10: 22nd
            11 => 23, // Zone 11: 23rd
            12 => 23, // Zone 12: 23rd
        ];

        $dueDay = $dueDayMap[$zone] ?? 15; // Default to 15 if zone not found
        $readingDate = Carbon::parse($readingDate);
        $dueDate = $readingDate->copy()->day($dueDay);

        // If the reading date is after the due day of the current month, set to next month's due day
        if ($readingDate->day > $dueDay || ($readingDate->day == $dueDay && $readingDate->hour >= 0)) {
            $dueDate = $dueDate->addMonth();
        }

        // Adjust for weekends (move to next working day)
        while ($dueDate->isWeekend()) {
            $dueDate = $dueDate->addDay();
        }

        return $dueDate->toDateString();
    }

    /**
     * Fix missing due dates for all records
     */
    private function fixMissingDueDates()
    {
        $recordsWithoutDueDate = MeterReading::with('user')
            ->whereNull('due_date')
            ->get();

        $fixedCount = 0;

        foreach ($recordsWithoutDueDate as $record) {
            try {
                $dueDate = $this->getDueDate($record->user, $record->reading_date);
                $record->due_date = $dueDate;
                $record->save();
                $fixedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to fix due date for record {$record->id}: " . $e->getMessage());
            }
        }

        return $fixedCount;
    }

    /**
     * Calculate surcharge and update status if overdue.
     */
    private function calculateSurchargeAndUpdateStatus($record, $dueDate)
    {
        if ($record->status === 'Paid' || !$dueDate) {
            return [
                'surcharge' => null,
                'total_amount' => $record->amount,
                'original_amount' => $record->amount
            ];
        }

        $dueDate = Carbon::parse($dueDate);
        $today = Carbon::now('Asia/Manila');

        // Check if record is overdue (the day after due date)
        if ($today->gt($dueDate)) {
            $originalAmount = $record->amount;
            $surcharge = round($originalAmount * 0.10, 2);
            $totalAmount = $originalAmount + $surcharge;

            // Auto-update status to Overdue if it's currently Pending
            if ($record->status === 'Pending') {
                $record->update([
                    'status' => 'Overdue',
                    'amount' => $totalAmount
                ]);
                $record->refresh();
            }

            return [
                'surcharge' => $surcharge,
                'total_amount' => $totalAmount,
                'original_amount' => $originalAmount
            ];
        }

        return [
            'surcharge' => null,
            'total_amount' => $record->amount,
            'original_amount' => $record->amount
        ];
    }

    /**
     * Auto-check and update overdue records
     */
    private function autoUpdateOverdueRecords()
    {
        $records = MeterReading::with('user')
            ->where('status', 'Pending')
            ->get();

        $updatedCount = 0;

        foreach ($records as $record) {
            // Ensure due date is set
            if (!$record->due_date) {
                $dueDate = $this->getDueDate($record->user, $record->reading_date);
                $record->due_date = $dueDate;
                $record->save();
            } else {
                $dueDate = $record->due_date;
            }

            $dueDate = Carbon::parse($dueDate);
            $today = Carbon::now('Asia/Manila');

            if ($today->gt($dueDate)) {
                $originalAmount = $record->amount;
                $surcharge = round($originalAmount * 0.10, 2);
                $totalAmount = $originalAmount + $surcharge;

                $record->update([
                    'status' => 'Overdue',
                    'amount' => $totalAmount
                ]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    public function index(Request $request)
    {
        // Fix missing due dates first
        $fixedDueDates = $this->fixMissingDueDates();

        // Log for debugging
        if ($fixedDueDates > 0) {
            Log::info("Fixed {$fixedDueDates} records with missing due dates");
        }

        // Auto-update overdue records every time we load the page
        $updatedOverdue = $this->autoUpdateOverdueRecords();

        // Build the query with relationships
        $query = MeterReading::with('user');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply month filter
        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('reading_date', $request->month);
        }

        // Apply year filter
        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('reading_date', $request->year);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        if ($sortField === 'name') {
            $query->join('users', 'meter_readings.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortDirection)
                ->orderBy('users.lastname', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Get paginated results
        $perPage = $request->get('perPage', 10);
        $records = $query->paginate($perPage);

        // Add due_date, surcharge, and total_amount to each record
        $records->getCollection()->transform(function ($record) {
            // DOUBLE CHECK: If due_date is still null, fix it immediately
            if (!$record->due_date) {
                $record->due_date = $this->getDueDate($record->user, $record->reading_date);
                $record->save();
            }

            $surchargeData = $this->calculateSurchargeAndUpdateStatus($record, $record->due_date);
            $record->surcharge = $surchargeData['surcharge'];
            $record->total_amount = $surchargeData['total_amount'];
            $record->original_amount = $surchargeData['original_amount'];

            return $record;
        });

        $serial_number = Auth::user()->serial_number;

        return Inertia::render('Admin/Records', [
            'serial_number' => $serial_number,
            'records' => $records,
            'filters' => $request->only(['search', 'status', 'month', 'year', 'perPage']),
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function show(MeterReading $record)
    {
        $record->load('user');

        // Ensure due date is set
        if (!$record->due_date) {
            $record->due_date = $this->getDueDate($record->user, $record->reading_date);
            $record->save();
        }

        $surchargeData = $this->calculateSurchargeAndUpdateStatus($record, $record->due_date);
        $record->surcharge = $surchargeData['surcharge'];
        $record->total_amount = $surchargeData['total_amount'];
        $record->original_amount = $surchargeData['original_amount'];

        // Return JSON if it's an AJAX request (for modal)
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json($record);
        }

        // Otherwise return the Inertia page
        return Inertia::render('Admin/Records/Show', [
            'record' => $record,
        ]);
    }

    public function edit(MeterReading $record)
    {
        $record->load('user');

        // Ensure due date is set
        if (!$record->due_date) {
            $record->due_date = $this->getDueDate($record->user, $record->reading_date);
            $record->save();
        }

        $surchargeData = $this->calculateSurchargeAndUpdateStatus($record, $record->due_date);
        $record->surcharge = $surchargeData['surcharge'];
        $record->total_amount = $surchargeData['total_amount'];
        $record->original_amount = $surchargeData['original_amount'];

        return Inertia::render('Admin/Records/Edit', [
            'record' => $record,
        ]);
    }

    public function update(Request $request, MeterReading $record)
    {
        $validated = $request->validate([
            'reading' => 'sometimes|numeric',
            'consumption' => 'sometimes|numeric',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|in:Paid,Pending,Overdue'
        ]);

        // Ensure due date is set before updating
        if (!$record->due_date) {
            $record->due_date = $this->getDueDate($record->user, $record->reading_date);
            $record->save();
        }

        $record->update($validated);

        return redirect()->route('admin.records.index')
            ->with('success', 'Record updated successfully.');
    }

    public function destroy(MeterReading $record)
    {
        $record->delete();

        return redirect()->back()
            ->with('success', 'Record deleted successfully.');
    }

    public function details(MeterReading $record)
    {
        $record->load('user');

        // Ensure due date is set
        if (!$record->due_date) {
            $record->due_date = $this->getDueDate($record->user, $record->reading_date);
            $record->save();
        }

        $surchargeData = $this->calculateSurchargeAndUpdateStatus($record, $record->due_date);
        $record->surcharge = $surchargeData['surcharge'];
        $record->total_amount = $surchargeData['total_amount'];
        $record->original_amount = $surchargeData['original_amount'];

        return response()->json($record);
    }

    /**
     * Manual endpoint to update overdue records and fix due dates
     */
    public function manualUpdateOverdue()
    {
        $fixedDueDates = $this->fixMissingDueDates();
        $updatedCount = $this->autoUpdateOverdueRecords();

        return redirect()->route('admin.records.index')
            ->with('success', "Fixed {$fixedDueDates} due dates and updated {$updatedCount} records to Overdue status.");
    }

    /**
     * Force fix all due dates - direct access endpoint
     */
    public function forceFixDueDates()
    {
        $fixedDueDates = $this->fixMissingDueDates();

        // Also check for any remaining null due dates and fix them
        $remainingNull = MeterReading::whereNull('due_date')->count();

        return redirect()->route('admin.records.index')
            ->with('success', "Fixed {$fixedDueDates} due dates. {$remainingNull} records still without due dates.");
    }

    /**
     * Export records in CSV or PDF format using month and year filters
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $filters = $request->only(['search', 'status', 'month', 'year']);

        // Build the query with relationships
        $query = MeterReading::with('user');

        // Apply month filter for export
        if (!empty($filters['month'])) {
            $query->whereMonth('reading_date', $filters['month']);
        }

        // Apply year filter for export
        if (!empty($filters['year'])) {
            $query->whereYear('reading_date', $filters['year']);
        }

        // If no month/year specified, default to current month
        if (empty($filters['month']) && empty($filters['year'])) {
            $currentMonth = Carbon::now('Asia/Manila')->format('m');
            $currentYear = Carbon::now('Asia/Manila')->format('Y');
            $query->whereMonth('reading_date', $currentMonth)
                ->whereYear('reading_date', $currentYear);
        }

        // Apply other filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Get all records for export
        $records = $query->orderBy('reading_date', 'desc')->get();

        // Calculate revenue statistics for the filtered period
        $paidRecords = $records->where('status', 'Paid');
        $totalRevenue = $paidRecords->sum('amount');
        $totalRecords = $records->count();
        $paidCount = $paidRecords->count();
        $pendingCount = $records->where('status', 'Pending')->count();
        $overdueCount = $records->where('status', 'Overdue')->count();

        // Determine period title based on filters
        $periodTitle = $this->getPeriodTitle($filters);

        $revenueStats = [
            'Period' => $periodTitle,
            'Total Revenue' => '₱' . number_format($totalRevenue, 2),
            'Total Records' => $totalRecords,
            'Paid Records' => $paidCount,
            'Pending Records' => $pendingCount,
            'Overdue Records' => $overdueCount,
            'Collection Rate' => $totalRecords > 0 ? round(($paidCount / $totalRecords) * 100, 2) . '%' : '0%',
        ];

        $timestamp = Carbon::now('Asia/Manila')->format('Y-m-d_H-i-s');
        $filename = "records_export_{$periodTitle}_{$timestamp}.{$format}";

        if ($format === 'csv') {
            return $this->exportCsv($records, $revenueStats, $filename, $periodTitle);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($records, $revenueStats, $filename, $periodTitle);
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Get formatted period title based on month/year filters
     */
    private function getPeriodTitle($filters)
    {
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;

        if ($month && $year) {
            // Specific month and year
            $date = Carbon::createFromDate($year, $month, 1);
            return $date->format('F Y');
        } elseif ($month && !$year) {
            // Month only (current year)
            $currentYear = Carbon::now('Asia/Manila')->format('Y');
            $date = Carbon::createFromDate($currentYear, $month, 1);
            return $date->format('F Y');
        } elseif (!$month && $year) {
            // Year only
            return "Year {$year}";
        } else {
            // No filters - current month
            return Carbon::now('Asia/Manila')->format('F Y');
        }
    }

    /**
     * Export data as CSV with period info
     */
    private function exportCsv($records, $revenueStats, $filename, $periodTitle)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($records, $revenueStats, $periodTitle) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Add period and revenue statistics section
            fputcsv($file, ['PERIOD REPORT: ' . $periodTitle]);
            fputcsv($file, []); // Empty line
            foreach ($revenueStats as $key => $value) {
                fputcsv($file, [$key, $value]);
            }
            fputcsv($file, []); // Empty line
            fputcsv($file, []); // Empty line

            // Add records section header
            fputcsv($file, ['RECORDS DATA']);
            fputcsv($file, []); // Empty line

            // Add column headers
            $headers = [
                'Customer Name',
                'Account Number',
                'Serial Number',
                'Reading',
                'Consumption',
                'Amount',
                'Surcharge',
                'Total Amount',
                'Due Date',
                'Status',
                'Reading Date'
            ];
            fputcsv($file, $headers);

            // Add data rows
            foreach ($records as $record) {
                // Ensure due date is set
                if (!$record->due_date) {
                    $record->due_date = $this->getDueDate($record->user, $record->reading_date);
                    $record->save();
                }

                $surchargeData = $this->calculateSurchargeAndUpdateStatus($record, $record->due_date);

                $row = [
                    'Customer Name' => $record->user->name . ' ' . $record->user->lastname,
                    'Account Number' => $record->user->account_number ?? 'N/A',
                    'Serial Number' => $record->user->serial_number ?? 'N/A',
                    'Reading' => $record->reading . ' m³',
                    'Consumption' => $record->consumption . ' m³',
                    'Amount' => '₱' . number_format($record->amount, 2),
                    'Surcharge' => $surchargeData['surcharge'] ? '₱' . number_format($surchargeData['surcharge'], 2) : 'None',
                    'Total Amount' => '₱' . number_format($surchargeData['total_amount'], 2),
                    'Due Date' => Carbon::parse($record->due_date)->format('M d, Y'),
                    'Status' => $record->status,
                    'Reading Date' => Carbon::parse($record->reading_date)->format('M d, Y'),
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export data as PDF with period info
     */
    private function exportPdf($records, $revenueStats, $filename, $periodTitle)
    {
        try {
            // Use Philippine time for export date
            $exportDate = Carbon::now('Asia/Manila')->format('F d, Y g:i A');

            $pdfData = [
                'records' => $records,
                'revenueStats' => $revenueStats,
                'exportDate' => $exportDate,
                'totalRecords' => $records->count(),
                'periodTitle' => $periodTitle,
            ];

            $pdf = PDF::loadView('admin.exports.records-pdf', $pdfData)
                ->setPaper('a4', 'landscape')
                ->setOption('defaultFont', 'Arial');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
