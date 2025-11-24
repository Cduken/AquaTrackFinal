<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     * Export records in multiple formats
     */
    public function export(Request $request)
{
    try {
        // Fix missing due dates first
        $this->fixMissingDueDates();

        // Auto-update overdue records
        $this->autoUpdateOverdueRecords();

        // Handle both GET and POST parameters
        $search = $request->get('search', $request->search);
        $status = $request->get('status', $request->status);
        $month = $request->get('month', $request->month);
        $year = $request->get('year', $request->year);
        $format = $request->get('format', $request->format ?? 'csv');

        // Build the query with relationships
        $query = MeterReading::with('user');

        // Apply search filter
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Apply month filter
        if (!empty($month)) {
            $query->whereMonth('reading_date', $month);
        }

        // Apply year filter
        if (!empty($year)) {
            $query->whereYear('reading_date', $year);
        }

        // Get all records (no pagination for export)
        $records = $query->orderBy('reading_date', 'desc')->get();

        // Rest of your export code remains the same...
        switch ($format) {
            case 'excel':
                return $this->exportExcel($records, $request);
            case 'pdf':
                return $this->exportPdf($records, $request);
            case 'csv':
            default:
                return $this->exportCsv($records, $request);
        }
    } catch (\Exception $e) {
        Log::error('Export failed: ' . $e->getMessage());

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Export failed: ' . $e->getMessage());
    }
}

    /**
     * Export records as CSV
     */
    private function exportCsv($records, $request)
    {
        $fileName = 'billing_records_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure Excel displays special characters correctly
            fwrite($file, "\xEF\xBB\xBF");

            // Add headers
            fputcsv($file, [
                'Account Number',
                'Customer Name',
                'Serial Number',
                'Zone',
                'Reading Date',
                'Due Date',
                'Current Reading (m³)',
                'Previous Reading (m³)',
                'Consumption (m³)',
                'Base Amount (₱)',
                'Surcharge (₱)',
                'Total Amount (₱)',
                'Status',
                'Days Status',
                'Contact Email',
                'Contact Phone'
            ]);

            // Calculate totals
            $totalAmount = 0;
            $totalSurcharge = 0;
            $totalFinalAmount = 0;

            // Add data
            foreach ($records as $record) {
                $finalAmount = $record->amount;
                $surcharge = 0;
                $originalAmount = $record->amount;

                // Calculate surcharge for overdue records
                if ($record->status !== 'Paid' && $record->due_date) {
                    $dueDate = Carbon::parse($record->due_date);
                    $today = Carbon::now('Asia/Manila');

                    if ($today->gt($dueDate)) {
                        $surcharge = round($record->amount * 0.10, 2);
                        $finalAmount = $record->amount + $surcharge;
                    }
                }

                // Add to totals
                $totalAmount += $originalAmount;
                $totalSurcharge += $surcharge;
                $totalFinalAmount += $finalAmount;

                $readingDate = $record->reading_date ? Carbon::parse($record->reading_date)->format('Y-m-d') : 'N/A';
                $dueDate = $record->due_date ? Carbon::parse($record->due_date)->format('Y-m-d') : 'N/A';

                // Calculate previous reading
                $previousReading = $record->reading - $record->consumption;

                fputcsv($file, [
                    $record->user->account_number ?? 'N/A',
                    $record->user->name . ' ' . $record->user->lastname,
                    $record->user->serial_number ?? 'N/A',
                    $record->user->zone ?? 'N/A',
                    $readingDate,
                    $dueDate,
                    $record->reading,
                    $previousReading,
                    $record->consumption,
                    number_format($originalAmount, 2),
                    number_format($surcharge, 2),
                    number_format($finalAmount, 2),
                    $record->status,
                    $this->getDaysUntilDue($record->due_date),
                    $record->user->email ?? 'N/A',
                    $record->user->phone ?? 'N/A'
                ]);
            }

            // Add summary row
            fputcsv($file, []); // Empty row
            fputcsv($file, ['SUMMARY', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Records:', $records->count(), '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Base Amount:', '', '', '', '', '', '', '', '', '₱' . number_format($totalAmount, 2), '', '', '', '', '', '']);
            fputcsv($file, ['Total Surcharge:', '', '', '', '', '', '', '', '', '', '₱' . number_format($totalSurcharge, 2), '', '', '', '', '']);
            fputcsv($file, ['Total Final Amount:', '', '', '', '', '', '', '', '', '', '', '₱' . number_format($totalFinalAmount, 2), '', '', '', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export records as Excel (XLSX) - Simple HTML table that Excel can open
     */
    private function exportExcel($records, $request)
    {
        $fileName = 'billing_records_' . date('Y-m-d_H-i-s') . '.xls';

        // Calculate totals
        $totalAmount = 0;
        $totalSurcharge = 0;
        $totalFinalAmount = 0;

        foreach ($records as $record) {
            $originalAmount = $record->amount;
            $surcharge = 0;
            $finalAmount = $record->amount;

            if ($record->status !== 'Paid' && $record->due_date) {
                $dueDate = Carbon::parse($record->due_date);
                $today = Carbon::now('Asia/Manila');
                if ($today->gt($dueDate)) {
                    $surcharge = round($record->amount * 0.10, 2);
                    $finalAmount = $record->amount + $surcharge;
                }
            }

            $totalAmount += $originalAmount;
            $totalSurcharge += $surcharge;
            $totalFinalAmount += $finalAmount;
        }

        $paidCount = $records->where('status', 'Paid')->count();
        $pendingCount = $records->where('status', 'Pending')->count();
        $overdueCount = $records->where('status', 'Overdue')->count();

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Billing Records Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .header h1 { margin: 0; color: #333; font-size: 24px; }
                        .header .subtitle { color: #666; margin-top: 5px; font-size: 14px; }
                        .summary { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
                        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
                        .summary-item { padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd; }
                        .summary-value { font-size: 16px; font-weight: bold; color: #333; }
                        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; margin-top: 20px; }
                        th { background-color: #f2f2f2; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left; }
                        td { border: 1px solid #ddd; padding: 8px; }
                        .summary-row { background-color: #f9f9f9; font-weight: bold; }
                        .total-row { background-color: #e8f4ff; font-weight: bold; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .status-paid { color: green; font-weight: bold; }
                        .status-pending { color: orange; font-weight: bold; }
                        .status-overdue { color: red; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Billing Records Report</h1>
                        <div class="subtitle">Generated on: ' . date('F j, Y g:i A') . '</div>
                    </div>

                    <div class="summary">
                        <strong>Report Summary:</strong>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div>Total Records: <span class="summary-value">' . $records->count() . '</span></div>
                                <div>Paid Records: <span class="summary-value">' . $paidCount . '</span></div>
                                <div>Pending Records: <span class="summary-value">' . $pendingCount . '</span></div>
                                <div>Overdue Records: <span class="summary-value">' . $overdueCount . '</span></div>
                            </div>
                            <div class="summary-item">
                                <div>Total Base Amount: <span class="summary-value">₱' . number_format($totalAmount, 2) . '</span></div>
                                <div>Total Surcharge: <span class="summary-value">₱' . number_format($totalSurcharge, 2) . '</span></div>
                                <div>Total Final Amount: <span class="summary-value">₱' . number_format($totalFinalAmount, 2) . '</span></div>
                            </div>
                        </div>
                    </div>

                    <table>
                        <tr>
                            <th>Account Number</th>
                            <th>Customer Name</th>
                            <th>Zone</th>
                            <th>Reading Date</th>
                            <th>Due Date</th>
                            <th>Current Reading</th>
                            <th>Consumption</th>
                            <th>Base Amount (₱)</th>
                            <th>Surcharge (₱)</th>
                            <th>Total Amount (₱)</th>
                            <th>Status</th>
                        </tr>';

        foreach ($records as $record) {
            $finalAmount = $record->amount;
            $surcharge = 0;
            $originalAmount = $record->amount;

            if ($record->status !== 'Paid' && $record->due_date) {
                $dueDate = Carbon::parse($record->due_date);
                $today = Carbon::now('Asia/Manila');
                if ($today->gt($dueDate)) {
                    $surcharge = round($record->amount * 0.10, 2);
                    $finalAmount = $record->amount + $surcharge;
                }
            }

            $readingDate = $record->reading_date ? Carbon::parse($record->reading_date)->format('Y-m-d') : 'N/A';
            $dueDate = $record->due_date ? Carbon::parse($record->due_date)->format('Y-m-d') : 'N/A';

            $html .= '<tr>
                        <td>' . ($record->user->account_number ?? 'N/A') . '</td>
                        <td>' . $record->user->name . ' ' . $record->user->lastname . '</td>
                        <td>' . ($record->user->zone ?? 'N/A') . '</td>
                        <td>' . $readingDate . '</td>
                        <td>' . $dueDate . '</td>
                        <td>' . $record->reading . '</td>
                        <td>' . $record->consumption . '</td>
                        <td class="text-right">' . number_format($originalAmount, 2) . '</td>
                        <td class="text-right">' . number_format($surcharge, 2) . '</td>
                        <td class="text-right">' . number_format($finalAmount, 2) . '</td>
                        <td class="text-center status-' . strtolower($record->status) . '">' . $record->status . '</td>
                    </tr>';
        }

        // Add summary row
        $html .= '<tr class="total-row">
                    <td colspan="7" class="text-right"><strong>GRAND TOTALS:</strong></td>
                    <td class="text-right"><strong>' . number_format($totalAmount, 2) . '</strong></td>
                    <td class="text-right"><strong>' . number_format($totalSurcharge, 2) . '</strong></td>
                    <td class="text-right"><strong>' . number_format($totalFinalAmount, 2) . '</strong></td>
                    <td class="text-center"><strong>Records: ' . $records->count() . '</strong></td>
                </tr>';

        $html .= '</table></body></html>';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response($html, 200, $headers);
    }

    /**
     * Export records as PDF - Simple HTML that can be printed as PDF
     */
    private function exportPdf($records, $request)
    {
        $fileName = 'billing_records_' . date('Y-m-d_H-i-s') . '.html';

        // Calculate totals
        $totalAmount = 0;
        $totalSurcharge = 0;
        $totalFinalAmount = 0;

        foreach ($records as $record) {
            $originalAmount = $record->amount;
            $surcharge = 0;
            $finalAmount = $record->amount;

            if ($record->status !== 'Paid' && $record->due_date) {
                $dueDate = Carbon::parse($record->due_date);
                $today = Carbon::now('Asia/Manila');
                if ($today->gt($dueDate)) {
                    $surcharge = round($record->amount * 0.10, 2);
                    $finalAmount = $record->amount + $surcharge;
                }
            }

            $totalAmount += $originalAmount;
            $totalSurcharge += $surcharge;
            $totalFinalAmount += $finalAmount;
        }

        $html = $this->generatePdfHtml($records, $totalAmount, $totalSurcharge, $totalFinalAmount);

        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response($html, 200, $headers);
    }

    /**
     * Generate HTML content for PDF export
     */
    private function generatePdfHtml($records, $totalAmount, $totalSurcharge, $totalFinalAmount)
    {
        $paidCount = $records->where('status', 'Paid')->count();
        $pendingCount = $records->where('status', 'Pending')->count();
        $overdueCount = $records->where('status', 'Overdue')->count();

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Billing Records Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { margin: 0; color: #333; font-size: 24px; }
                .header .subtitle { color: #666; margin-top: 5px; font-size: 14px; }
                .summary { margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; }
                .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
                .summary-item { padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd; }
                .summary-value { font-size: 16px; font-weight: bold; color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
                th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .status-paid { color: green; font-weight: bold; }
                .status-pending { color: orange; font-weight: bold; }
                .status-overdue { color: red; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
                .total-row { background-color: #e8f4ff; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Billing Records Report</h1>
                <div class="subtitle">Generated on ' . date('F j, Y g:i A') . '</div>
            </div>

            <div class="summary">
                <strong>Report Summary:</strong>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div>Total Records: <span class="summary-value">' . $records->count() . '</span></div>
                        <div>Paid Records: <span class="summary-value">' . $paidCount . '</span></div>
                        <div>Pending Records: <span class="summary-value">' . $pendingCount . '</span></div>
                        <div>Overdue Records: <span class="summary-value">' . $overdueCount . '</span></div>
                    </div>
                    <div class="summary-item">
                        <div>Total Base Amount: <span class="summary-value">₱' . number_format($totalAmount, 2) . '</span></div>
                        <div>Total Surcharge: <span class="summary-value">₱' . number_format($totalSurcharge, 2) . '</span></div>
                        <div>Total Final Amount: <span class="summary-value">₱' . number_format($totalFinalAmount, 2) . '</span></div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Account No.</th>
                        <th>Customer Name</th>
                        <th>Zone</th>
                        <th>Reading Date</th>
                        <th>Due Date</th>
                        <th class="text-center">Reading (m³)</th>
                        <th class="text-center">Consumption (m³)</th>
                        <th class="text-right">Base Amount (₱)</th>
                        <th class="text-right">Surcharge (₱)</th>
                        <th class="text-right">Total Amount (₱)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($records as $record) {
            $finalAmount = $record->amount;
            $surcharge = 0;
            $originalAmount = $record->amount;

            if ($record->status !== 'Paid' && $record->due_date) {
                $dueDate = Carbon::parse($record->due_date);
                $today = Carbon::now('Asia/Manila');
                if ($today->gt($dueDate)) {
                    $surcharge = round($record->amount * 0.10, 2);
                    $finalAmount = $record->amount + $surcharge;
                }
            }

            $readingDate = $record->reading_date ? Carbon::parse($record->reading_date)->format('M j, Y') : 'N/A';
            $dueDate = $record->due_date ? Carbon::parse($record->due_date)->format('M j, Y') : 'N/A';

            $html .= '
                    <tr>
                        <td>' . ($record->user->account_number ?? 'N/A') . '</td>
                        <td>' . $record->user->name . ' ' . $record->user->lastname . '</td>
                        <td>' . ($record->user->zone ?? 'N/A') . '</td>
                        <td>' . $readingDate . '</td>
                        <td>' . $dueDate . '</td>
                        <td class="text-center">' . $record->reading . '</td>
                        <td class="text-center">' . $record->consumption . '</td>
                        <td class="text-right">' . number_format($originalAmount, 2) . '</td>
                        <td class="text-right">' . number_format($surcharge, 2) . '</td>
                        <td class="text-right">' . number_format($finalAmount, 2) . '</td>
                        <td class="text-center status-' . strtolower($record->status) . '">' . $record->status . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="7" class="text-right"><strong>GRAND TOTALS:</strong></td>
                        <td class="text-right"><strong>₱' . number_format($totalAmount, 2) . '</strong></td>
                        <td class="text-right"><strong>₱' . number_format($totalSurcharge, 2) . '</strong></td>
                        <td class="text-right"><strong>₱' . number_format($totalFinalAmount, 2) . '</strong></td>
                        <td class="text-center"><strong>Records: ' . $records->count() . '</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="footer">
                <p>Generated by Water Billing System | Page 1 of 1</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    private function getDaysUntilDue($dueDate)
    {
        if (!$dueDate) return 'N/A';

        $today = Carbon::now('Asia/Manila');
        $due = Carbon::parse($dueDate);
        $diffDays = $today->diffInDays($due, false);

        if ($diffDays === 0) return 'Due today';
        if ($diffDays === 1) return 'Due tomorrow';
        if ($diffDays > 1) return "Due in {$diffDays} days";
        if ($diffDays === -1) return "Overdue by 1 day";
        return "Overdue by " . abs($diffDays) . " days";
    }
}
