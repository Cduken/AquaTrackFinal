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
                $record->save(); // Use save() instead of update to ensure it works
                $fixedCount++;
            } catch (\Exception $e) {
                // Log error but continue with other records
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
        $today = Carbon::today('America/Los_Angeles');

        // Check if record is overdue (the day after due date)
        if ($today->gt($dueDate)) {
            $originalAmount = $record->amount; // This is the base amount without surcharge
            $surcharge = round($originalAmount * 0.10, 2); // 10% surcharge
            $totalAmount = $originalAmount + $surcharge;

            // Auto-update status to Overdue if it's currently Pending
            if ($record->status === 'Pending') {
                $record->update([
                    'status' => 'Overdue',
                    'amount' => $totalAmount // Save the total amount with surcharge to database
                ]);
                $record->refresh(); // Refresh the record to get updated status and amount
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
            $today = Carbon::today('America/Los_Angeles');

            if ($today->gt($dueDate)) {
                $originalAmount = $record->amount;
                $surcharge = round($originalAmount * 0.10, 2);
                $totalAmount = $originalAmount + $surcharge;

                $record->update([
                    'status' => 'Overdue',
                    'amount' => $totalAmount // Save the total amount with surcharge
                ]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    public function index(Request $request)
    {
        // Fix missing due dates first - FORCE it to run
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
}
