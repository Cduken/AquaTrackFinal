<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;

class StaffReadingController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Reading');
    }

    public function search(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            $users = User::where('role', 'customer')
                ->where(function ($q) use ($query) {
                    // Search by name (first or last name)
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('lastname', 'like', "%{$query}%");

                    // Search by serial number
                    $q->orWhere('serial_number', 'like', "%{$query}%");
                })
                ->select([
                    'id',
                    'name',
                    'lastname',
                    'account_number',
                    'zone',
                    'barangay',
                    'municipality',
                    'province',
                    'phone',
                    'date_installed',
                    'brand',
                    'serial_number',
                    'size',
                    'avatar',
                ])
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'lastname' => $user->lastname,
                        'account_number' => $user->account_number,
                        'address' => implode(', ', array_filter([
                            $user->zone,
                            $user->barangay,
                            $user->municipality,
                            $user->province
                        ])),
                        'phone' => $user->phone,
                        'date_installed' => $user->date_installed,
                        'brand' => $user->brand,
                        'serial_number' => $user->serial_number,
                        'size' => $user->size,
                        'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getUserDetails($userId)
    {
        $user = User::where('id', $userId)
            ->where('role', 'customer')
            ->firstOrFail();

        return response()->json([
            'name' => $user->name,
            'lastname' => $user->lastname,
            'account_number' => $user->account_number,
            'address' => implode(', ', array_filter([
                $user->zone,
                $user->barangay,
                $user->municipality,
                $user->province
            ])),
            'phone' => $user->phone,
            'date_installed' => $user->date_installed ?? 'Not available',
            'brand' => $user->brand ?? 'Not specified',
            'serial_number' => $user->serial_number ?? 'N/A',
            'size' => $user->size ?? 'N/A',
            'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null
        ]);
    }

    public function getPreviousReadings($userId)
    {
        try {
            // Validate the user exists and is a customer
            $user = User::where('id', $userId)
                ->where('role', 'customer')
                ->firstOrFail();

            $readings = MeterReading::where('user_id', $userId)
                ->orderBy('reading_date', 'desc')
                ->limit(12)
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->id,
                        'billing_month' => $reading->billing_month,
                        'reading_date' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A',
                        'reading' => $reading->reading,
                        'previous_reading' => $reading->previous_reading,
                        'consumption' => $reading->consumption,
                        'amount' => $reading->amount,
                        'status' => $reading->status,
                        'year' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y') : date('Y')
                    ];
                });

            return response()->json($readings);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching previous readings: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function updateReading(Request $request, $readingId)
    {
        try {
            $validated = $request->validate([
                'reading' => 'required|numeric|min:0',
                'amount' => 'required|numeric|min:0',
                'status' => 'required|in:Pending,Paid',
                'consumption' => 'required|numeric|min:0',
            ]);

            $reading = MeterReading::findOrFail($readingId);

            // Check if staff has permission to edit this reading
            if ($reading->staff_id !== Auth::id()) {
                return response()->json([
                    'error' => 'You are not authorized to edit this reading'
                ], 403);
            }

            $reading->update([
                'reading' => $validated['reading'],
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'consumption' => $validated['consumption'],
                'updated_at' => now(),
            ]);

            // Refresh the reading to get updated data
            $reading->refresh();

            return response()->json([
                'message' => 'Reading updated successfully',
                'reading' => [
                    'id' => $reading->id,
                    'billing_month' => $reading->billing_month,
                    'reading_date' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A',
                    'reading' => $reading->reading,
                    'previous_reading' => $reading->previous_reading,
                    'consumption' => $reading->consumption,
                    'amount' => $reading->amount,
                    'status' => $reading->status,
                    'year' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y') : date('Y')
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Reading not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating reading: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function storeReading(Request $request)
    {
        Log::info('=== STORE READING START ===');
        Log::info('Request data:', $request->all());

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'billing_month' => 'required|string',
                'reading_date' => 'required|date',
                'reading' => 'required|numeric|min:0',
                'previous_reading' => 'nullable|numeric|min:0',
            ]);

            Log::info('Validation passed:', $validated);

            // Parse the reading date to get year
            $readingDate = Carbon::parse($validated['reading_date']);
            $readingYear = $readingDate->format('Y');

            // Check if reading already exists for this month and year
            $existingReading = MeterReading::where('user_id', $validated['user_id'])
                ->where('billing_month', $validated['billing_month'])
                ->whereYear('reading_date', $readingYear)
                ->first();

            if ($existingReading) {
                return response()->json([
                    'error' => 'A reading already exists for this billing month and year'
                ], 422);
            }

            // Fetch the user to get the zone
            $user = User::findOrFail($validated['user_id']);
            Log::info('User found:', ['id' => $user->id, 'zone' => $user->zone]);

            // Determine the correct previous reading
            $previousReadingValue = $this->determinePreviousReading(
                $validated['user_id'],
                $validated['previous_reading'] ?? null,
                $readingDate
            );

            Log::info('Previous reading determined:', ['value' => $previousReadingValue]);

            // For new users (previous reading is 0), allow any reading >= 0
            // For existing users, current reading must be >= previous reading
            if ($previousReadingValue > 0 && $validated['reading'] < $previousReadingValue) {
                return response()->json([
                    'error' => 'Current reading must be greater than or equal to previous reading'
                ], 422);
            }

            $consumption = $validated['reading'] - $previousReadingValue;
            $amount = $this->calculateBillAmount($consumption);

            Log::info('Calculated values:', ['consumption' => $consumption, 'amount' => $amount]);

            // **FIXED: Calculate due date with proper error handling**
            $dueDate = $this->getDueDate($user, $validated['reading_date']);
            Log::info('Due date calculated:', ['due_date' => $dueDate]);

            // **FIXED: Ensure due_date is NEVER null with proper fallback**
            if (empty($dueDate) || $dueDate === null || $dueDate === 'NULL' || $dueDate === 'null') {
                Log::warning('Primary due date calculation failed, using fallback');
                $dueDate = $this->calculateFallbackDueDate($user, $validated['reading_date']);
                Log::info('Fallback due date:', ['fallback_due_date' => $dueDate]);
            }

            // **FIXED: Final safety check - if still empty, use reading date + 15 days**
            if (empty($dueDate)) {
                Log::error('All due date calculations failed, using ultimate fallback');
                $dueDate = Carbon::parse($validated['reading_date'])->addDays(15)->format('Y-m-d');
                Log::info('Ultimate fallback due date:', ['due_date' => $dueDate]);
            }

            Log::info('Final data to be inserted:', [
                'user_id' => $validated['user_id'],
                'staff_id' => Auth::id(),
                'billing_month' => $validated['billing_month'],
                'reading_date' => $validated['reading_date'],
                'previous_reading' => $previousReadingValue,
                'reading' => $validated['reading'],
                'consumption' => $consumption,
                'amount' => $amount,
                'status' => 'Pending',
                'due_date' => $dueDate,
            ]);

            // **Create the reading with explicit due_date**
            $newReading = MeterReading::create([
                'user_id' => $validated['user_id'],
                'staff_id' => Auth::id(),
                'billing_month' => $validated['billing_month'],
                'reading_date' => $validated['reading_date'],
                'previous_reading' => $previousReadingValue,
                'reading' => $validated['reading'],
                'consumption' => $consumption,
                'amount' => $amount,
                'status' => 'Pending',
                'due_date' => $dueDate ?? Carbon::parse($validated['reading_date'])->addDays(15)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Reading created successfully:', [
                'id' => $newReading->id,
                'due_date' => $newReading->due_date
            ]);

            // **Verify the reading was created with due_date**
            $createdReading = MeterReading::find($newReading->id);
            Log::info('Verified reading from database:', [
                'id' => $createdReading->id,
                'due_date' => $createdReading->due_date
            ]);

            return response()->json([
                'message' => 'Reading saved successfully',
                'reading' => $newReading,
                'due_date_stored' => $createdReading->due_date // Return the actual stored due_date
            ]);
        } catch (\Exception $e) {
            Log::error('Error in storeReading: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to save reading: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine the correct previous reading value
     */
    private function determinePreviousReading($userId, $providedPreviousReading, $currentReadingDate)
    {
        // If previous reading is provided, use it
        if ($providedPreviousReading !== null) {
            return (float) $providedPreviousReading;
        }

        // Get the most recent reading before the current reading date
        $previousReading = MeterReading::where('user_id', $userId)
            ->where('reading_date', '<', $currentReadingDate->format('Y-m-d'))
            ->orderBy('reading_date', 'desc')
            ->first();

        if ($previousReading) {
            return $previousReading->reading;
        }

        // If no previous reading found, this is the first reading - return 0
        return 0;
    }

    /**
     * Calculate bill amount based on tiered pricing
     * 1-10 m³ = ₱132 (fixed)
     * 11-20 m³ = ₱14 per m³
     * 21-30 m³ = ₱14.85 per m³
     * 31-40 m³ = ₱16 per m³
     * 41+ m³ = ₱17.25 per m³
     */
    private function calculateBillAmount($consumption)
    {
        if ($consumption <= 0) {
            return 0;
        }

        if ($consumption <= 10) {
            return 132.00; // Fixed rate for first 10 m³
        }

        $amount = 132.00; // Base amount for first 10 m³

        if ($consumption > 10 && $consumption <= 20) {
            $amount += ($consumption - 10) * 14;
        } elseif ($consumption > 20 && $consumption <= 30) {
            $amount += (10 * 14) + (($consumption - 20) * 14.85);
        } elseif ($consumption > 30 && $consumption <= 40) {
            $amount += (10 * 14) + (10 * 14.85) + (($consumption - 30) * 16);
        } else {
            $amount += (10 * 14) + (10 * 14.85) + (10 * 16) + (($consumption - 40) * 17.25);
        }

        return round($amount, 2);
    }

    /**
     * Get the due date based on the user's zone and reading date.
     */
    /**
     * Get the due date based on the user's zone and reading date.
     */
    private function getDueDate($user, $readingDate)
    {
        try {
            Log::info('getDueDate called with:', [
                'user_zone' => $user->zone,
                'reading_date' => $readingDate
            ]);

            $zone = $user->zone;

            // Handle null or empty zone
            if ($zone === null || $zone === '' || $zone === 'NULL' || $zone === 'null') {
                Log::warning('Zone is null or empty, using default zone 1');
                $zoneNumber = 1;
            } else {
                // Convert to string for consistent processing
                $zoneString = (string) $zone;

                // Extract numbers - handle formats like "Zone 1", "1", "zone1", "ZONE 1", etc.
                preg_match('/(\d+)/', $zoneString, $matches);

                if (isset($matches[1])) {
                    $zoneNumber = (int) $matches[1];
                    Log::info('Zone number extracted:', ['zone_number' => $zoneNumber]);
                } else {
                    Log::warning('Cannot extract zone number from: "' . $zoneString . '", using default zone 1');
                    $zoneNumber = 1;
                }
            }

            // Ensure zone is between 1-12
            if ($zoneNumber < 1 || $zoneNumber > 12) {
                Log::warning('Zone ' . $zoneNumber . ' out of range, using default zone 1');
                $zoneNumber = 1;
            }

            // Define due dates strictly per zone rules
            $dueDayMap = [
                1 => 15,  // Zone 1: 15th
                2 => 16,  // Zone 2: 16th
                3 => 16,  // Zone 3: 16th
                4 => 17,  // Zone 4: 17th
                5 => 18,  // Zone 5: 18th
                6 => 19,  // Zone 6: 19th
                7 => 19,  // Zone 7: 19th
                8 => 20,  // Zone 8: 20th
                9 => 21,  // Zone 9: 21st
                10 => 22, // Zone 10: 22nd
                11 => 23, // Zone 11: 23rd
                12 => 23, // Zone 12: 23rd
            ];

            $dueDay = $dueDayMap[$zoneNumber] ?? 15;
            $readingDate = Carbon::parse($readingDate);

            Log::info('Due day calculation:', [
                'zone_number' => $zoneNumber,
                'due_day' => $dueDay,
                'reading_date' => $readingDate->toDateString()
            ]);

            // Create due date in the SAME MONTH as the reading date
            $dueDate = $readingDate->copy()->day($dueDay);

            // If the reading date is after the due day of the current month, set to next month's due day
            if ($readingDate->day > $dueDay) {
                Log::info('Reading date after due day, moving to next month');
                $dueDate = $dueDate->addMonth();
            }

            // Adjust for weekends (move to next working day)
            $originalDueDate = $dueDate->copy();
            while ($dueDate->isWeekend()) {
                $dueDate = $dueDate->addDay();
            }

            if ($originalDueDate != $dueDate) {
                Log::info('Due date adjusted for weekend', [
                    'original' => $originalDueDate->toDateString(),
                    'adjusted' => $dueDate->toDateString()
                ]);
            }

            $finalDueDate = $dueDate->toDateString();
            Log::info('Final due date:', ['due_date' => $finalDueDate]);

            return $finalDueDate;
        } catch (\Exception $e) {
            Log::error('Error calculating due date: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            // Return null to trigger fallback
            return null;
        }
    }

    /**
     * Fallback due date calculation when primary method fails
     */
    private function calculateFallbackDueDate($user, $readingDate)
    {
        try {
            $readingDate = Carbon::parse($readingDate);

            // Extract zone number from user zone
            $zone = $user->zone;
            $zoneNumber = 1; // default

            if ($zone !== null && $zone !== '' && $zone !== 'NULL' && $zone !== 'null') {
                preg_match('/(\d+)/', (string)$zone, $matches);
                if (isset($matches[1])) {
                    $zoneNumber = (int)$matches[1];
                }
            }

            // Ensure zone is between 1-12
            $zoneNumber = max(1, min(12, $zoneNumber));

            // Simple due date mapping
            $dueDayMap = [
                1 => 15,
                2 => 16,
                3 => 16,
                4 => 17,
                5 => 18,
                6 => 19,
                7 => 19,
                8 => 20,
                9 => 21,
                10 => 22,
                11 => 23,
                12 => 23,
            ];

            $dueDay = $dueDayMap[$zoneNumber] ?? 15;

            // Create due date in current month
            $dueDate = $readingDate->copy()->day($dueDay);

            // If reading date is after due day, move to next month
            if ($readingDate->day > $dueDay) {
                $dueDate = $dueDate->addMonth();
            }

            // Adjust for weekends
            while ($dueDate->isWeekend()) {
                $dueDate = $dueDate->addDay();
            }

            return $dueDate->toDateString(); // This always returns string

        } catch (\Exception $e) {
            Log::error('Fallback due date calculation failed: ' . $e->getMessage());
            // Ultimate fallback: 15 days from reading date - ALWAYS returns string
            return Carbon::parse($readingDate)->addDays(15)->toDateString();
        }
    }

    // In StaffReadingController
    public function viewCustomerReading($readingId)
    {
        try {
            $staff = Auth::user();

            if ($staff->role !== 'staff') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $reading = MeterReading::find($readingId);
            if (!$reading) {
                return response()->json(['error' => 'Reading not found'], 404);
            }

            // Mark as viewed by staff - this triggers notification
            $reading->update([
                'viewed_by_staff' => true,
                'staff_viewed_at' => now(),
            ]);

            Log::info('Staff viewed customer water reading', [
                'staff_id' => $staff->id,
                'reading_id' => $readingId,
                'customer_id' => $reading->user_id
            ]);

            return response()->json([
                'success' => true,
                'reading' => $reading
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing reading: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to view reading'], 500);
        }
    }
}
