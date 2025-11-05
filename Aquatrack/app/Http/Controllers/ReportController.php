<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $zones = [
        'Zone 1' => ['Poblacion Sur'],
        'Zone 2' => ['Poblacion Centro'],
        'Zone 3' => ['Poblacion Centro (Zone 3)'],
        'Zone 4' => ['Poblacion Norte'],
        'Zone 5' => ['Candajec', 'Buangan'],
        'Zone 6' => ['Bonbon'],
        'Zone 7' => ['Bonbon'],
        'Zone 8' => ['Nahawan'],
        'Zone 9' => ['Caboy', 'Villaflor', 'Cantuyoc'],
        'Zone 10' => ['Bacani', 'Mataub', 'Comaang', 'Tangaran'],
        'Zone 11' => ['Cantuyoc', 'Nahawan'],
        'Zone 12' => ['Lajog', 'Buacao'],
    ];

    protected $base_priorities = [
        'Burst pipe' => 'high',
        'No water supply' => 'high',
        'Cloudy or dirty water' => 'medium',
        'Smelly water' => 'medium',
        'Clogged pipes' => 'medium',
        'Rusty water' => 'low',
        'Low water pressure' => 'low',
        'Hot water issues' => 'low',
        'Running toilet' => 'low',
        'others' => 'medium',
    ];

    /**
     * Get comprehensive report statistics
     */
    public function getReportStats()
    {
        try {
            // Count only main reports (not merged references)
            $totalReports = Report::whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->count();

            $resolvedReports = Report::where('status', 'resolved')
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->count();

            $pendingReports = Report::where('status', 'pending')
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->count();

            $inProgressReports = Report::where('status', 'in_progress')
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->count();

            $resolutionPercentage = $totalReports > 0
                ? round(($resolvedReports / $totalReports) * 100, 1)
                : 0;

            return [
                'total_reports' => $totalReports,
                'resolved_reports' => $resolvedReports,
                'pending_reports' => $pendingReports,
                'in_progress_reports' => $inProgressReports,
                'resolution_percentage' => $resolutionPercentage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch report stats: ' . $e->getMessage());
            return [
                'total_reports' => 0,
                'resolved_reports' => 0,
                'pending_reports' => 0,
                'in_progress_reports' => 0,
                'resolution_percentage' => 0,
            ];
        }
    }

    /**
     * Get dashboard statistics combining users and reports
     */
    public function getDashboardStats()
    {
        try {
            $activeUsers = User::where('enabled', true)->count();
            $totalUsers = User::count();
            $newUsersThisMonth = User::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            $reportStats = $this->getReportStats();

            return array_merge([
                'active_users' => $activeUsers,
                'total_users' => $totalUsers,
                'new_users_this_month' => $newUsersThisMonth,
            ], $reportStats);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return [
                'active_users' => 0,
                'total_users' => 0,
                'new_users_this_month' => 0,
                'total_reports' => 0,
                'resolved_reports' => 0,
                'pending_reports' => 0,
                'in_progress_reports' => 0,
                'resolution_percentage' => 0,
            ];
        }
    }


    /**
     * Handle offline report sync
     */
    public function syncOfflineReports(Request $request)
    {
        try {
            Log::debug('Offline report sync started', [
                'input' => $request->all(),
                'files_count' => $request->file('photos') ? count($request->file('photos')) : 0,
            ]);

            // Use the same validation as the regular store method
            $validated = $request->validate([
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'zone' => 'required|string',
                'barangay' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $zone = $request->input('zone');
                        if (!isset($this->zones[$zone]) || !in_array($value, $this->zones[$zone])) {
                            $fail('The selected barangay is not valid for the chosen zone.');
                        }
                    },
                ],
                'purok' => 'required|string|max:255',
                'water_issue_type' => [
                    'required',
                    'string',
                    'max:255',
                    'in:"Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",others',
                ],
                'custom_water_issue' => 'nullable|required_if:water_issue_type,others|string|max:100',
                'description' => 'required|string',
                'photos' => 'required|array|min:1|max:5',
                'photos.*' => [
                    'file',
                    'mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm',
                    function ($attribute, $value, $fail) {
                        $originalName = $value->getClientOriginalName();
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
                        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                        if (!in_array($extension, array_merge($imageExtensions, $videoExtensions))) {
                            $fail('The file must be an image (jpeg,png,jpg,gif,webp) or video (mp4,mov,avi,webm).');
                        }
                        if (in_array($extension, $imageExtensions) && $value->getSize() > 5 * 1024 * 1024) {
                            $fail('The photo must not exceed 5MB.');
                        }
                        if (in_array($extension, $videoExtensions) && $value->getSize() > 25 * 1024 * 1024) {
                            $fail('The video must not exceed 25MB.');
                        }
                    },
                ],
                'reporter_name' => 'required|string|max:255',
                'reporter_phone' => 'nullable|string|max:11',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            // Use the same logic as the store method
            $latitude = (float)$validated['latitude'];
            $longitude = (float)$validated['longitude'];
            $issueType = $validated['water_issue_type'];
            $zone = $validated['zone'];
            $barangay = $validated['barangay'];
            $purok = $validated['purok'];

            if (!$this->isValidClarinCoordinates($latitude, $longitude)) {
                return response()->json([
                    'success' => false,
                    'message' => 'GPS coordinates are outside the expected range for Clarin, Bohol. Please ensure your location services are accurate.',
                ], 422);
            }

            // Find existing report to merge with
            $existingReport = $this->findExistingReportForMerging($zone, $barangay, $issueType, $latitude, $longitude, $purok);

            $trackingCode = null;
            $reportId = null;
            $isMerged = false;
            $mainReport = null;

            if ($existingReport) {
                // MERGE WITH EXISTING REPORT
                $trackingCode = $existingReport->tracking_code;
                $reportId = $existingReport->id;
                $isMerged = true;
                $mainReport = $existingReport;

                // Update reporter names
                $existingReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $newReporter = trim($validated['reporter_name']);

                if (!in_array($newReporter, $existingReporters)) {
                    $existingReporters[] = $newReporter;
                }

                $existingReport->reporter_name = implode(', ', $existingReporters);

                // FIXED: PROPER USER TYPES HANDLING FOR ALL SCENARIOS
                $existingUserTypes = $existingReport->user_types ? json_decode($existingReport->user_types, true) : [];
                if (!is_array($existingUserTypes)) {
                    $existingUserTypes = [];
                }

                $currentUserType = Auth::check() ? 'Registered' : 'Guest';
                $currentReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $newReporter = trim($validated['reporter_name']);

                // Check if this is a new reporter
                $isNewReporter = !in_array($newReporter, $currentReporters);

                if ($isNewReporter) {
                    // Add new reporter and their type
                    $existingUserTypes[] = $currentUserType;
                } else {
                    // Update existing reporter type if needed
                    $reporterIndex = array_search($newReporter, $currentReporters);
                    if ($reporterIndex !== false && isset($existingUserTypes[$reporterIndex])) {
                        // Only upgrade from Guest to Registered, never downgrade
                        if ($existingUserTypes[$reporterIndex] === 'Guest' && $currentUserType === 'Registered') {
                            $existingUserTypes[$reporterIndex] = 'Registered';
                        }
                    }
                }

                // FIXED: Rebuild user_types array from scratch to ensure accuracy
                $allReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $rebuildUserTypes = [];

                foreach ($allReporters as $reporter) {
                    $reporterType = 'Guest'; // Default to Guest

                    // Check if this reporter is in our existing user types
                    $reporterIndex = array_search($reporter, $allReporters);
                    if (isset($existingUserTypes[$reporterIndex]) && $existingUserTypes[$reporterIndex] === 'Registered') {
                        $reporterType = 'Registered';
                    }
                    // If not in existing types, check if reporter is registered user
                    elseif ($this->isRegisteredUser($reporter)) {
                        $reporterType = 'Registered';
                    }
                    // Special case: if this is the current user adding the report
                    elseif ($reporter === $newReporter && $currentUserType === 'Registered') {
                        $reporterType = 'Registered';
                    }

                    $rebuildUserTypes[] = $reporterType;
                }

                // Store the rebuilt user_types
                $existingReport->user_types = json_encode($rebuildUserTypes);

                // Update reporter phones
                if ($validated['reporter_phone']) {
                    $existingPhones = $existingReport->reporter_phone ? array_filter(array_map('trim', explode(',', $existingReport->reporter_phone))) : [];
                    $newPhone = trim($validated['reporter_phone']);

                    if (!in_array($newPhone, $existingPhones)) {
                        $existingPhones[] = $newPhone;
                        $existingReport->reporter_phone = implode(', ', $existingPhones);
                    }
                }

                // Update additional tracking codes
                $additionalCodes = $existingReport->additional_tracking_codes ? json_decode($existingReport->additional_tracking_codes, true) : [];
                if (!is_array($additionalCodes)) $additionalCodes = [];

                $newTrackingCode = 'AQT' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
                $additionalCodes[] = $newTrackingCode;
                $existingReport->additional_tracking_codes = json_encode(array_unique($additionalCodes));

                // Append new description
                $newDescription = "Additional Report From {$validated['reporter_name']}\n-> {$validated['description']}\n";
                $existingReport->description = $existingReport->description . "\n" . $newDescription;

                // Add photos to the main report
                foreach ($request->file('photos') as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $extension = $photo->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;
                    $path = $photo->storeAs('report-photos', $filename, 'public');

                    ReportPhoto::create([
                        'report_id' => $existingReport->id,
                        'path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $photo->getClientMimeType(),
                        'size' => $photo->getSize(),
                    ]);
                }

                // For registered users, update user_id in the main report
                if (Auth::check() && !$existingReport->user_id) {
                    $existingReport->user_id = Auth::id();
                }

                $existingReport->save();

                Log::info('Offline report merged', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                    'merged_reporters' => $existingReport->reporter_name,
                ]);
            } else {
                // CREATE NEW REPORT
                $datePart = now()->format('Ymd');
                $randomPart = strtoupper(Str::random(4));
                $trackingCode = 'AQT' . $datePart . '-' . $randomPart;

                // $userTypes = [Auth::check() ? 'Registered' : 'Guest'];

                $isAuthenticated = Auth::check();
                $userTypes = [$isAuthenticated ? 'Registered' : 'Guest'];

                $reportData = [
                    'municipality' => $validated['municipality'],
                    'province' => $validated['province'],
                    'barangay' => $validated['barangay'],
                    'purok' => $validated['purok'],
                    'zone' => $validated['zone'],
                    'water_issue_type' => $validated['water_issue_type'],
                    'custom_water_issue' => $validated['custom_water_issue'] ?? null,
                    'description' => $validated['description'],
                    'reporter_name' => $validated['reporter_name'],
                    'reporter_phone' => $validated['reporter_phone'] ?? null,
                    'user_id' => Auth::check() ? Auth::id() : null, // THIS MUST BE SET
                    'status' => 'pending',
                    'tracking_code' => $trackingCode,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'priority' => $this->base_priorities[$issueType] ?? 'low',
                    'is_merged_reference' => false,
                    'user_types' => json_encode($userTypes),
                ];

                $report = Report::create($reportData);
                $reportId = $report->id;
                $mainReport = $report;

                foreach ($request->file('photos') as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $extension = $photo->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;
                    $path = $photo->storeAs('report-photos', $filename, 'public');

                    ReportPhoto::create([
                        'report_id' => $reportId,
                        'path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $photo->getClientMimeType(),
                        'size' => $photo->getSize(),
                    ]);
                }

                Log::info('New offline report created', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                ]);
            }

            // Update priority based on number of reporters
            if ($mainReport) {
                $base_priority = $this->base_priorities[$issueType] ?? 'low';
                $reporters = explode(',', $mainReport->reporter_name);
                $num_reporters = count(array_filter(array_map('trim', $reporters)));
                $priority = $base_priority;

                if ($base_priority === 'low') {
                    if ($num_reporters >= 4) {
                        $priority = 'high';
                    } elseif ($num_reporters >= 2) {
                        $priority = 'medium';
                    }
                } elseif ($base_priority === 'medium' && $num_reporters >= 3) {
                    $priority = 'high';
                }

                if ($mainReport->priority != $priority) {
                    $mainReport->priority = $priority;
                    $mainReport->save();
                }
            }

            // Log activity
            if (!$isMerged) {
                Activity::create([
                    'event' => 'report_created',
                    'description' => 'New report submitted from offline sync: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'tracking_code' => $trackingCode,
                        'zone' => $validated['zone'],
                        'status' => 'pending',
                        'user_type' => Auth::check() ? 'registered' : 'guest',
                        'synced_from_offline' => true,
                    ],
                ]);
            } else {
                Activity::create([
                    'event' => 'report_merged',
                    'description' => 'Offline report merged into existing report: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'main_tracking_code' => $trackingCode,
                        'zone' => $validated['zone'],
                        'reporter_added' => $validated['reporter_name'],
                        'user_type' => Auth::check() ? 'registered' : 'guest',
                        'synced_from_offline' => true,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $isMerged ? 'Offline report merged successfully' : 'Offline report submitted successfully',
                'trackingCode' => $trackingCode,
                'dateSubmitted' => now()->toISOString(),
                'reportId' => $reportId,
                'isMerged' => $isMerged,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Offline report validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for offline report.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Offline report sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync offline report. Please try again.',
            ], 500);
        }
    }

    /**
     * Detect if a reporter name belongs to a registered user - FIXED for accuracy
     */
    protected function isRegisteredUser($reporterName)
    {
        try {
            $cleanName = trim($reporterName);
            if (empty($cleanName)) {
                return false;
            }

            // First, check if this is the current authenticated user
            if (Auth::check() && Auth::user()->name === $cleanName) {
                return true;
            }

            // Check if this reporter name matches any registered user by name
            $user = User::where('name', $cleanName)->first();
            if ($user) {
                return true;
            }

            // Try case-insensitive match for name
            $user = User::whereRaw('LOWER(name) = LOWER(?)', [$cleanName])->first();
            if ($user) {
                return true;
            }

            // For merged reports, we need to be more strict
            // Only return true if we can definitively match a registered user
            return false;
        } catch (\Exception $e) {
            Log::error('Error checking registered user', [
                'reporterName' => $reporterName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enhanced formatUserTypes method - FIXED for accurate Hybrid detection
     */
    protected function formatUserTypes($userTypesJson, $userId = null, $reporterName = null)
    {
        // If we have valid user_types data, use that
        if ($userTypesJson) {
            $userTypes = json_decode($userTypesJson, true);

            if (is_array($userTypes) && !empty($userTypes)) {
                // Count occurrences of each type
                $typeCounts = array_count_values($userTypes);
                $hasRegistered = isset($typeCounts['Registered']);
                $hasGuest = isset($typeCounts['Guest']);

                // Only Hybrid if we have BOTH types
                if ($hasRegistered && $hasGuest) {
                    return 'Hybrid';
                }
                // Only Registered if all are Registered
                elseif ($hasRegistered && !$hasGuest) {
                    return 'Registered';
                }
                // Only Guest if all are Guest
                else {
                    return 'Guest';
                }
            }
        }

        // Enhanced fallback logic for merged reports - FIXED
        if ($reporterName && str_contains($reporterName, ',')) {
            $reporters = array_filter(array_map('trim', explode(',', $reporterName)));
            $hasRegistered = false;
            $hasGuest = false;

            foreach ($reporters as $reporter) {
                if ($this->isRegisteredUser($reporter)) {
                    $hasRegistered = true;
                } else {
                    $hasGuest = true;
                }

                // If we found both types, we can break early
                if ($hasRegistered && $hasGuest) {
                    break;
                }
            }

            // FIXED: Only return Hybrid if we have BOTH registered AND guest
            if ($hasRegistered && $hasGuest) {
                return 'Hybrid';
            }
            // If we only found registered users (all registered)
            elseif ($hasRegistered) {
                return 'Registered';
            }
            // If we only found guests (all guests)
            else {
                return 'Guest';
            }
        }

        // Single reporter fallback
        if ($userId) {
            return 'Registered';
        }

        // If no userId but reporter name might be registered
        if ($reporterName && $this->isRegisteredUser($reporterName)) {
            return 'Registered';
        }

        return 'Guest';
    }
    /**
     * Find existing report for merging with improved GPS accuracy
     */
    protected function findExistingReportForMerging($zone, $barangay, $issueType, $latitude, $longitude, $purok)
    {
        try {
            // Enhanced radius calculation - more precise for urban areas
            $radius = 0.05; // Reduced to 50 meters for better accuracy
            $earthRadius = 6371000; // meters

            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                throw new \Exception('Invalid latitude or longitude values');
            }

            // Validate coordinates are reasonable
            if (!$this->isValidClarinCoordinates($latitude, $longitude)) {
                Log::warning('Invalid coordinates provided for merging', [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                return null;
            }

            Log::debug('Finding existing report for merging', [
                'zone' => $zone,
                'barangay' => $barangay,
                'issueType' => $issueType,
                'purok' => $purok,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'search_radius_meters' => $radius * 1000
            ]);

            // First, try exact matches with same location and issue type
            $exactMatches = Report::where('zone', $zone)
                ->where('barangay', $barangay)
                ->where('purok', $purok)
                ->where('water_issue_type', $issueType)
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->where('status', '!=', 'resolved')
                ->get();

            Log::debug('Exact matches found', ['count' => $exactMatches->count()]);

            // If we have exact matches, return the first one
            if ($exactMatches->isNotEmpty()) {
                Log::debug('Returning exact match', ['report_id' => $exactMatches->first()->id]);
                return $exactMatches->first();
            }

            // Enhanced proximity-based matching with better distance calculation
            $potentialReports = Report::where('zone', $zone)
                ->where('barangay', $barangay)
                ->where('water_issue_type', $issueType)
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->where('status', '!=', 'resolved')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            Log::debug('Potential reports for proximity check', ['count' => $potentialReports->count()]);

            $matchingReports = $potentialReports->filter(function ($report) use ($latitude, $longitude, $radius, $earthRadius) {
                try {
                    // Use Haversine formula for accurate distance calculation
                    $latFrom = deg2rad((float)$latitude);
                    $lonFrom = deg2rad((float)$longitude);
                    $latTo = deg2rad((float)$report->latitude);
                    $lonTo = deg2rad((float)$report->longitude);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                    $distance = $angle * $earthRadius;

                    $isWithinRadius = $distance <= ($radius * 1000); // Convert km to meters

                    Log::debug('Proximity check', [
                        'report_id' => $report->id,
                        'distance_meters' => $distance,
                        'within_radius' => $isWithinRadius,
                        'threshold_meters' => $radius * 1000
                    ]);

                    return $isWithinRadius;
                } catch (\Exception $e) {
                    Log::error('Error calculating distance', [
                        'error' => $e->getMessage(),
                        'report_id' => $report->id
                    ]);
                    return false;
                }
            });

            $result = $matchingReports->first();
            Log::debug('Proximity match result', [
                'found' => !is_null($result),
                'report_id' => $result ? $result->id : null,
                'total_checked' => $potentialReports->count(),
                'within_radius' => $matchingReports->count()
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Duplicate check failed', [
                'error' => $e->getMessage(),
                'zone' => $zone,
                'barangay' => $barangay,
                'issueType' => $issueType,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'purok' => $purok,
            ]);
            return null;
        }
    }

    public function store(Request $request)
    {
        try {
            Log::debug('Report submission started', [
                'input' => $request->all(),
                'files' => $request->file('photos') ? array_map(function ($file) {
                    return [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                    ];
                }, $request->file('photos')) : [],
            ]);

            $validated = $request->validate([
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'zone' => 'required|string',
                'barangay' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $zone = $request->input('zone');
                        if (!isset($this->zones[$zone]) || !in_array($value, $this->zones[$zone])) {
                            $fail('The selected barangay is not valid for the chosen zone.');
                        }
                    },
                ],
                'purok' => 'required|string|max:255',
                'water_issue_type' => [
                    'required',
                    'string',
                    'max:255',
                    'in:"Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",others',
                ],
                'custom_water_issue' => 'nullable|required_if:water_issue_type,others|string|max:100',
                'description' => 'required|string',
                'photos' => 'required|array|min:1|max:5',
                'photos.*' => [
                    'file',
                    'mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm',
                    function ($attribute, $value, $fail) {
                        $originalName = $value->getClientOriginalName();
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
                        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                        if (!in_array($extension, array_merge($imageExtensions, $videoExtensions))) {
                            $fail('The file must be an image (jpeg,png,jpg,gif,webp) or video (mp4,mov,avi,webm).');
                        }
                        if (in_array($extension, $imageExtensions) && $value->getSize() > 5 * 1024 * 1024) {
                            $fail('The photo must not exceed 5MB.');
                        }
                        if (in_array($extension, $videoExtensions) && $value->getSize() > 25 * 1024 * 1024) {
                            $fail('The video must not exceed 25MB.');
                        }
                    },
                ],
                'reporter_name' => 'required|string|max:255',
                'reporter_phone' => 'nullable|string|max:11',
                'latitude' => [
                    'required',
                    'numeric',
                    'between:9.0,10.0', // Clarin, Bohol approximate latitude range
                    function ($attribute, $value, $fail) {
                        if (abs($value) < 1) {
                            $fail('GPS coordinates appear invalid. Please ensure location services are enabled.');
                        }
                    },
                ],
                'longitude' => [
                    'required',
                    'numeric',
                    'between:123.0,125.0', // Clarin, Bohol approximate longitude range
                    function ($attribute, $value, $fail) {
                        if (abs($value) < 1) {
                            $fail('GPS coordinates appear invalid. Please ensure location services are enabled.');
                        }
                    },
                ],
            ]);

            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $issueType = $validated['water_issue_type'];
            $zone = $validated['zone'];
            $barangay = $validated['barangay'];
            $purok = $validated['purok'];

            Log::debug('Checking for existing reports to merge', [
                'zone' => $zone,
                'barangay' => $barangay,
                'purok' => $purok,
                'issue_type' => $issueType
            ]);

            // Find existing report to merge with - RELAXED CONDITIONS
            $existingReport = $this->findExistingReportForMerging($zone, $barangay, $issueType, $latitude, $longitude, $purok);

            $trackingCode = null;
            $reportId = null;
            $isMerged = false;
            $mainReport = null;

            if ($existingReport) {
                Log::debug('Found existing report to merge with', [
                    'existing_report_id' => $existingReport->id,
                    'existing_tracking_code' => $existingReport->tracking_code
                ]);

                // MERGE WITH EXISTING REPORT
                $trackingCode = $existingReport->tracking_code;
                $reportId = $existingReport->id;
                $isMerged = true;
                $mainReport = $existingReport;

                // Update reporter names
                $existingReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $newReporter = trim($validated['reporter_name']);

                if (!in_array($newReporter, $existingReporters)) {
                    $existingReporters[] = $newReporter;
                    $existingReport->reporter_name = implode(', ', $existingReporters);
                }

                // In your store method, replace the user type handling with this:

                // FIXED: PROPER USER TYPES HANDLING FOR ALL SCENARIOS
                $existingUserTypes = $existingReport->user_types ? json_decode($existingReport->user_types, true) : [];
                if (!is_array($existingUserTypes)) {
                    $existingUserTypes = [];
                }

                $currentUserType = Auth::check() ? 'Registered' : 'Guest';
                $currentReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $newReporter = trim($validated['reporter_name']);

                // Check if this is a new reporter
                $isNewReporter = !in_array($newReporter, $currentReporters);

                if ($isNewReporter) {
                    // Add new reporter and their type
                    $existingUserTypes[] = $currentUserType;
                } else {
                    // Update existing reporter type if needed
                    $reporterIndex = array_search($newReporter, $currentReporters);
                    if ($reporterIndex !== false && isset($existingUserTypes[$reporterIndex])) {
                        // Only upgrade from Guest to Registered, never downgrade
                        if ($existingUserTypes[$reporterIndex] === 'Guest' && $currentUserType === 'Registered') {
                            $existingUserTypes[$reporterIndex] = 'Registered';
                        }
                    }
                }

                // FIXED: Rebuild user_types array from scratch to ensure accuracy
                $allReporters = array_filter(array_map('trim', explode(',', $existingReport->reporter_name)));
                $rebuildUserTypes = [];

                foreach ($allReporters as $reporter) {
                    $reporterType = 'Guest'; // Default to Guest

                    // Check if this reporter is a registered user
                    if ($this->isRegisteredUser($reporter)) {
                        $reporterType = 'Registered';
                    }
                    // Special case: if this is the current user adding the report
                    elseif ($reporter === $newReporter && $currentUserType === 'Registered') {
                        $reporterType = 'Registered';
                    }

                    $rebuildUserTypes[] = $reporterType;
                }

                // Store the rebuilt user_types
                $existingReport->user_types = json_encode($rebuildUserTypes);

                // Update reporter phones
                if ($validated['reporter_phone']) {
                    $existingPhones = $existingReport->reporter_phone ? array_filter(array_map('trim', explode(',', $existingReport->reporter_phone))) : [];
                    $newPhone = trim($validated['reporter_phone']);

                    if (!in_array($newPhone, $existingPhones)) {
                        $existingPhones[] = $newPhone;
                        $existingReport->reporter_phone = implode(', ', $existingPhones);
                    }
                }

                // Update additional tracking codes
                $additionalCodes = $existingReport->additional_tracking_codes ? json_decode($existingReport->additional_tracking_codes, true) : [];
                if (!is_array($additionalCodes)) $additionalCodes = [];

                $newTrackingCode = 'AQT' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
                $additionalCodes[] = $newTrackingCode;
                $existingReport->additional_tracking_codes = json_encode(array_unique($additionalCodes));

                // Append new description
                $newDescription = "Additional Report From {$validated['reporter_name']}\n-> {$validated['description']}\n";
                $existingReport->description = $existingReport->description . "\n" . $newDescription;

                // Add photos to the main report
                foreach ($request->file('photos') as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $extension = $photo->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;
                    $path = $photo->storeAs('report-photos', $filename, 'public');

                    ReportPhoto::create([
                        'report_id' => $existingReport->id,
                        'path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $photo->getClientMimeType(),
                        'size' => $photo->getSize(),
                    ]);
                }

                // For registered users, update user_id in the main report
                if (Auth::check() && !$existingReport->user_id) {
                    $existingReport->user_id = Auth::id();
                }

                $existingReport->save();

                Log::info('Report merged successfully', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                    'merged_reporters' => $existingReport->reporter_name,
                    'user_types' => $existingReport->user_types,
                ]);
            } else {
                Log::debug('No existing report found, creating new report');
                // CREATE NEW REPORT
                $datePart = now()->format('Ymd');
                $randomPart = strtoupper(Str::random(4));
                $trackingCode = 'AQT' . $datePart . '-' . $randomPart;

                $isAuthenticated = Auth::check();
                $userTypes = [$isAuthenticated ? 'Registered' : 'Guest'];

                $reportData = [
                    'municipality' => $validated['municipality'],
                    'province' => $validated['province'],
                    'barangay' => $validated['barangay'],
                    'purok' => $validated['purok'],
                    'zone' => $validated['zone'],
                    'water_issue_type' => $validated['water_issue_type'],
                    'custom_water_issue' => $validated['custom_water_issue'] ?? null,
                    'description' => $validated['description'],
                    'reporter_name' => $validated['reporter_name'],
                    'reporter_phone' => $validated['reporter_phone'] ?? null,
                    'user_id' => $isAuthenticated ? Auth::id() : null,
                    'status' => 'pending',
                    'tracking_code' => $trackingCode,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'priority' => $this->base_priorities[$issueType] ?? 'low',
                    'is_merged_reference' => false,
                    'user_types' => json_encode($userTypes),
                ];

                $report = Report::create($reportData);
                $reportId = $report->id;
                $mainReport = $report;

                foreach ($request->file('photos') as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $extension = $photo->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;
                    $path = $photo->storeAs('report-photos', $filename, 'public');

                    ReportPhoto::create([
                        'report_id' => $reportId,
                        'path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $photo->getClientMimeType(),
                        'size' => $photo->getSize(),
                    ]);
                }

                Log::info('New report created', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                ]);
            }

            // Update priority based on number of reporters
            if ($mainReport) {
                $this->updateReportPriority($mainReport, $issueType);
            }

            // Log activity
            if (!$isMerged) {
                Activity::create([
                    'event' => 'report_created',
                    'description' => 'New report submitted: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'tracking_code' => $trackingCode,
                        'zone' => $validated['zone'],
                        'status' => 'pending',
                        'user_type' => Auth::check() ? 'registered' : 'guest',
                    ],
                ]);
            } else {
                Activity::create([
                    'event' => 'report_merged',
                    'description' => 'Report merged into existing report: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'main_tracking_code' => $trackingCode,
                        'zone' => $validated['zone'],
                        'reporter_added' => $validated['reporter_name'],
                        'user_type' => Auth::check() ? 'registered' : 'guest',
                    ],
                ]);
            }

            if (Auth::check()) {
                return redirect()->route('customer.reports')
                    ->with('swal', [
                        'icon' => 'success',
                        'title' => $isMerged ? 'Report Merged' : 'Report Submitted',
                        'text' => $isMerged ? 'Your report has been merged with an existing one! Use the main tracking code to monitor progress.' : 'Your report has been submitted successfully!',
                        'trackingCode' => $trackingCode,
                        'isMerged' => $isMerged,
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => $isMerged ? 'Report merged successfully. Use the main tracking code to monitor progress.' : 'Report submitted successfully',
                'trackingCode' => $trackingCode,
                'dateSubmitted' => now()->toISOString(),
                'reportId' => $reportId,
                'isMerged' => $isMerged,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors(), 'request' => $request->all()]);
            if (Auth::check()) {
                return redirect()->back()->withErrors($e->errors())->withInput();
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form for errors.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Report submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.',
            ], 500);
        }
    }

    /**
     * Temporary method to fix existing user_types data
     */
    public function fixUserTypes()
    {
        $reports = Report::whereNull('deleted_at')
            ->where('is_merged_reference', false)
            ->get();

        foreach ($reports as $report) {
            $correctUserType = $this->determineUserTypeFromReporters($report);

            if (str_contains($report->reporter_name, ',')) {
                // For merged reports, build proper user_types array
                $reporters = array_filter(array_map('trim', explode(',', $report->reporter_name)));
                $userTypes = [];

                foreach ($reporters as $reporter) {
                    $userTypes[] = $this->isRegisteredUser($reporter) ? 'Registered' : 'Guest';
                }

                $report->user_types = json_encode($userTypes);
            } else {
                // For single reporter
                $userType = $report->user_id ? 'Registered' : 'Guest';
                $report->user_types = json_encode([$userType]);
            }

            $report->save();

            Log::info('Fixed user types for report', [
                'report_id' => $report->id,
                'reporter_name' => $report->reporter_name,
                'user_types' => $report->user_types,
                'formatted_type' => $correctUserType
            ]);
        }

        return response()->json(['message' => 'User types fixed for ' . $reports->count() . ' reports']);
    }

    /**
     * Comprehensive fix for all user types
     */
    public function fixAllUserTypes()
    {
        $reports = Report::whereNull('deleted_at')
            ->where('is_merged_reference', false)
            ->get();

        $fixedCount = 0;
        $hybridCount = 0;

        foreach ($reports as $report) {
            $originalUserTypes = $report->user_types;
            $correctType = $this->determineUserTypeFromReporters($report);

            // Rebuild user_types array accurately
            $reporters = $report->reporter_name ?
                array_filter(array_map('trim', explode(',', $report->reporter_name))) : [];
            $userTypes = [];

            foreach ($reporters as $reporter) {
                $userTypes[] = $this->isRegisteredUser($reporter) ? 'Registered' : 'Guest';
            }

            $report->user_types = json_encode($userTypes);
            $report->save();

            if ($correctType === 'Hybrid') {
                $hybridCount++;
            }
            $fixedCount++;

            Log::info('Fixed user types', [
                'report_id' => $report->id,
                'reporter_name' => $report->reporter_name,
                'original_types' => $originalUserTypes,
                'new_types' => $report->user_types,
                'formatted_type' => $correctType
            ]);
        }

        return response()->json([
            'message' => "Fixed user types for {$fixedCount} reports ({$hybridCount} Hybrid)"
        ]);
    }

    /**
     * Validate if coordinates are within reasonable range for Clarin, Bohol
     */
    protected function isValidClarinCoordinates($latitude, $longitude)
    {
        // Clarin, Bohol approximate coordinates: 9.9620° N, 124.0250° E
        $clarinLatitude = 9.9620;
        $clarinLongitude = 124.0250;

        // Allowable radius from Clarin center (in degrees)
        // Approximately 10km radius for municipality coverage
        $allowedRadius = 0.1; // ~11km in degrees

        $latDiff = abs($latitude - $clarinLatitude);
        $lonDiff = abs($longitude - $clarinLongitude);

        $isValid = ($latDiff <= $allowedRadius) && ($lonDiff <= $allowedRadius);

        if (!$isValid) {
            Log::warning('GPS coordinates outside Clarin range', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'expected_lat' => $clarinLatitude,
                'expected_lon' => $clarinLongitude,
                'lat_diff' => $latDiff,
                'lon_diff' => $lonDiff,
                'allowed_radius' => $allowedRadius
            ]);
        }

        return $isValid;
    }



    /**
     * Update report priority based on number of reporters
     */
    protected function updateReportPriority($report, $issueType)
    {
        $base_priority = $this->base_priorities[$issueType] ?? 'low';
        $reporters = explode(',', $report->reporter_name);
        $num_reporters = count(array_filter(array_map('trim', $reporters)));
        $priority = $base_priority;

        if ($base_priority === 'low') {
            if ($num_reporters >= 4) {
                $priority = 'high';
            } elseif ($num_reporters >= 2) {
                $priority = 'medium';
            }
        } elseif ($base_priority === 'medium' && $num_reporters >= 3) {
            $priority = 'high';
        }

        if ($report->priority != $priority) {
            $report->priority = $priority;
            $report->save();

            Log::info('Priority updated', [
                'report_id' => $report->id,
                'old_priority' => $base_priority,
                'new_priority' => $priority,
                'num_reporters' => $num_reporters,
            ]);
        }
    }

    public function destroy(Request $request, Report $report)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
            ]);

            // Store the original tracking code before deletion
            $trackingCode = $report->tracking_code;

            $report->status = "Deleted: " . $validated['reason'];
            $report->save();
            $report->delete();

            Activity::create([
                'event' => 'report_deleted',
                'description' => "Report deleted with reason: {$validated['reason']}",
                'subject_type' => get_class($report),
                'subject_id' => $report->id,
                'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                'causer_id' => Auth::id(),
                'properties' => [
                    'tracking_code' => $trackingCode,
                    'reason' => $validated['reason'],
                ],
            ]);

            return redirect()->route('admin.reports')->with([
                'swal' => [
                    'icon' => 'success',
                    'title' => 'Report Deleted',
                    'text' => 'Report deleted successfully.',
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Report deletion validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->with([
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Validation Error',
                    'text' => 'Please provide a valid reason for deletion.',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Report deletion failed', [
                'error' => $e->getMessage(),
                'report_id' => $report->id,
            ]);
            return redirect()->back()->with([
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Deletion Failed',
                    'text' => 'Failed to delete report. Please try again.',
                ],
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Reports/Index', [
            'zones' => $this->zones,
        ]);
    }

    public function success(Request $request)
    {
        if (!$request->session()->has('trackingCode')) {
            return redirect()->route('home');
        }

        return Inertia::render('Reports/Success', [
            'trackingCode' => $request->session()->get('trackingCode'),
            'dateSubmitted' => $request->session()->get('dateSubmitted'),
        ]);
    }

    public function customerIndex(Request $request)
    {
        try {
            // For customer side, show ONLY reports where user_id matches the current logged-in user
            $reports = Report::with(['photos'])
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false)
                ->where('user_id', Auth::id()) // STRICTLY only reports with this user_id
                ->latest()
                ->paginate(10);

            // Debug logging to see what's happening
            Log::debug('Customer reports query results', [
                'user_id' => Auth::id(),
                'reports_count' => $reports->count(),
                'report_ids' => $reports->pluck('id')->toArray()
            ]);

            return Inertia::render('Customer/Reports', [
                'reports' => $reports,
                'zones' => $this->zones,
                'swal' => $request->session()->get('swal'),
            ]);
        } catch (\Exception $e) {
            Log::error('Customer reports fetch failed', ['error' => $e->getMessage()]);
            return Inertia::render('Customer/Reports', [
                'reports' => [],
                'zones' => $this->zones,
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to load reports.',
                ],
            ]);
        }
    }

    /**
     * Display the main welcome page with hero section
     */
    public function index()
    {
        $dashboardStats = $this->getDashboardStats();

        return Inertia::render('Welcome', [
            'canLogin' => true,
            'canRegister' => true,
            'dashboardStats' => [
                'active_users' => $dashboardStats['active_users'],
                'resolution_percentage' => $dashboardStats['resolution_percentage'],
                'resolved_reports' => $dashboardStats['resolved_reports'],
                'total_reports' => $dashboardStats['total_reports'],
            ],
        ]);
    }

    public function adminIndex(Request $request)
    {
        try {
            // SIMPLE FIX: Only show main reports that are NOT merged references
            $query = Report::with(['photos', 'user'])
                ->whereNull('deleted_at')
                ->where('is_merged_reference', false) // Only main reports
                ->latest();

            if ($request->userType === 'guest') {
                $query->whereNull('user_id');
            } elseif ($request->userType === 'authenticated') {
                $query->whereNotNull('user_id');
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $search = $request->search;
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('tracking_code', 'like', "%{$search}%")
                        ->orWhere('reporter_name', 'like', "%{$search}%")
                        ->orWhere('water_issue_type', 'like', "%{$search}%")
                        ->orWhere('custom_water_issue', 'like', "%{$search}%");
                });
            }


            $reports = $query->paginate(10)
                ->appends($request->query())
                ->through(function ($report) {
                    // FIXED: Use the corrected method to determine user types
                    $report->formatted_user_types = $this->determineUserTypeFromReporters($report);

                    // Build accurate user_types array for modal display
                    $reporters = $report->reporter_name ?
                        array_filter(array_map('trim', explode(',', $report->reporter_name))) : [];
                    $userTypes = [];

                    foreach ($reporters as $reporter) {
                        $userTypes[] = $this->isRegisteredUser($reporter) ? 'Registered' : 'Guest';
                    }

                    $report->user_types = json_encode($userTypes);

                    // Add avatar_url to user if exists
                    if ($report->user && $report->user->avatar) {
                        $report->user->avatar_url = Storage::url($report->user->avatar);
                    }

                    return $report;
                });

            // Handle pre-selected report
            $selectedReport = null;
            if ($request->filled('report_id')) {
                $selectedReport = Report::with(['photos', 'user'])->find($request->input('report_id'));
                if (!$selectedReport || $selectedReport->deleted_at) {
                    $selectedReport = null;
                } else {
                    // Format user_types for selected report - PASS ALL REQUIRED PARAMETERS
                    $selectedReport->formatted_user_types = $this->formatUserTypes(
                        $selectedReport->user_types,
                        $selectedReport->user_id,
                        $selectedReport->reporter_name
                    );
                }
            }

            $reportStats = $this->getReportStats();

            return Inertia::render('Admin/Reports', [
                'reports' => $reports,
                'filters' => $request->only(['userType', 'status', 'search']),
                'canEdit' => true,
                'canDelete' => true,
                'swal' => $request->session()->get('swal'),
                'selectedReport' => $selectedReport,
                'report_id' => $request->input('report_id'),
                'reportStats' => $reportStats,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin reports fetch failed', ['error' => $e->getMessage()]);
            return Inertia::render('Admin/Reports', [
                'reports' => [],
                'filters' => $request->only(['userType', 'status', 'search']),
                'canEdit' => true,
                'canDelete' => true,
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to load reports.',
                ],
                'report_id' => $request->input('report_id'),
                'reportStats' => $this->getReportStats(),
            ]);
        }
    }

    /**
     * Determine user type by analyzing all reporters in a report - ENHANCED
     */
    protected function determineUserTypeFromReporters($report)
    {
        // If we have user_id, it means at least one registered user is involved
        $hasRegisteredUser = !is_null($report->user_id);

        // If it's a merged report with multiple reporters
        if (str_contains($report->reporter_name, ',')) {
            $reporters = array_filter(array_map('trim', explode(',', $report->reporter_name)));
            $registeredCount = 0;
            $guestCount = 0;

            foreach ($reporters as $reporter) {
                if ($this->isRegisteredUser($reporter)) {
                    $registeredCount++;
                } else {
                    $guestCount++;
                }
            }

            Log::debug('User type analysis', [
                'report_id' => $report->id,
                'reporters' => $reporters,
                'registered_count' => $registeredCount,
                'guest_count' => $guestCount,
                'user_id' => $report->user_id
            ]);

            // Only return Hybrid if we have BOTH registered AND guest reporters
            if ($registeredCount > 0 && $guestCount > 0) {
                return 'Hybrid';
            }
            // If we only have registered reporters (all registered)
            elseif ($registeredCount > 0) {
                return 'Registered';
            }
            // If we only have guest reporters (all guests)
            else {
                return 'Guest';
            }
        }

        // Single reporter - use user_id as primary indicator
        if ($report->user_id) {
            return 'Registered';
        }

        // For single reporter without user_id, check if name matches registered user
        if ($report->reporter_name && $this->isRegisteredUser($report->reporter_name)) {
            return 'Registered';
        }

        // Default to Guest
        return 'Guest';
    }

    public function track(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                return Inertia::render('Reports/Track');
            }

            $request->validate([
                'tracking_code' => 'required|string',
            ]);

            $report = Report::with(['photos'])
                ->withTrashed()
                ->where(function ($query) use ($request) {
                    $query->where('tracking_code', $request->tracking_code)
                        ->orWhereJsonContains('additional_tracking_codes', $request->tracking_code);
                })
                ->first();

            if (!$report) {
                return Inertia::render('Reports/Track', [
                    'swal' => [
                        'icon' => 'error',
                        'title' => 'Error',
                        'text' => 'Report not found with this tracking code.',
                    ],
                ]);
            }

            if ($report->trashed()) {
                return Inertia::render('Reports/Track', [
                    'report' => null,
                    'isDeleted' => true,
                    'deletionReason' => $report->status,
                ]);
            }

            // Format user_types for display
            $report->formatted_user_types = $this->formatUserTypes($report->user_types);

            return Inertia::render('Reports/Track', [
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Report tracking failed', ['error' => $e->getMessage()]);
            return Inertia::render('Reports/Track', [
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Report not found or an error occurred.',
                ],
            ]);
        }
    }

    public function findByTrackingCode(Request $request)
    {
        try {
            $request->validate([
                'tracking_code' => 'required|string',
            ]);

            $report = Report::with(['photos'])
                ->withTrashed()
                ->where(function ($query) use ($request) {
                    $query->where('tracking_code', $request->tracking_code)
                        ->orWhereJsonContains('additional_tracking_codes', $request->tracking_code);
                })
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report found with this tracking code.',
                ], 404);
            }

            if ($report->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This report has been deleted.',
                    'deleted' => true,
                    'reason' => $report->status,
                ], 410);
            }

            // Format user_types for display
            $report->formatted_user_types = $this->formatUserTypes($report->user_types);

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            Log::error('Find by tracking code failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve report.',
            ], 500);
        }
    }

    public function updateStatus(Request $request, Report $report)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,resolved',
            ]);

            $oldStatus = $report->status;
            $newStatus = $request->status;

            $trackingCode = $report->tracking_code;
            $linkedReports = Report::where(function ($query) use ($trackingCode) {
                $query->where('tracking_code', $trackingCode)
                    ->orWhereJsonContains('additional_tracking_codes', $trackingCode);
            })->get();

            foreach ($linkedReports as $linkedReport) {
                // ✅ ADD THIS: Reset viewed_by_user to trigger notification
                $linkedReport->status = $newStatus;
                $linkedReport->viewed_by_user = false; // This makes notification appear as unread
                $linkedReport->save();

                Activity::create([
                    'event' => 'report_status_changed',
                    'description' => "Report status changed from {$oldStatus} to {$newStatus}",
                    'subject_type' => get_class($linkedReport),
                    'subject_id' => $linkedReport->id,
                    'causer_type' => get_class(Auth::user()),
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'tracking_code' => $linkedReport->tracking_code,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ],
                ]);

                // ✅ ADD LOGGING TO CONFIRM NOTIFICATION TRIGGER
                Log::info('Report status updated - notification triggered', [
                    'report_id' => $linkedReport->id,
                    'user_id' => $linkedReport->user_id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'viewed_by_user_reset' => true
                ]);
            }

            return redirect()->route('admin.reports')->with([
                'swal' => [
                    'icon' => 'success',
                    'title' => 'Status Updated',
                    'text' => 'Report status updated successfully for all linked reports! Customers will be notified.',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Status update failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.reports')->with([
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to update status: ' . $e->getMessage(),
                ],
            ]);
        }
    }



    public function getReportersData(Request $request)
    {
        try {
            $reporters = $request->input('reporters', []);
            $existingUserTypes = $request->input('existingUserTypes', []);

            $result = [];

            foreach ($reporters as $index => $reporterName) {
                $user = User::where('name', trim($reporterName))->first();

                $reporterType = $existingUserTypes[$index] ?? ($user ? 'Registered' : 'Guest');

                $result[] = [
                    'name' => $reporterName,
                    'type' => $reporterType,
                    'avatar' => $user && $user->avatar ? Storage::url($user->avatar) : null,
                    'isRegistered' => $reporterType === 'Registered',
                    'index' => $index
                ];
            }

            return response()->json([
                'success' => true,
                'reporters' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get reporters data', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reporter data'
            ], 500);
        }
    }


    /**
     * Get statistics API endpoint (for AJAX updates)
     */
    public function getStats()
    {
        try {
            $stats = $this->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'data' => $this->getDashboardStats()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            // Build the query based on filters
            $query = Report::with('user');

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('tracking_code', 'like', "%{$search}%")
                        ->orWhere('reporter_name', 'like', "%{$search}%")
                        ->orWhere('water_issue_type', 'like', "%{$search}%")
                        ->orWhere('custom_water_issue', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Apply user type filter
            if ($request->has('userType') && !empty($request->userType) && $request->userType !== 'all') {
                if ($request->userType === 'guest') {
                    $query->whereNull('user_id');
                } elseif ($request->userType === 'authenticated') {
                    $query->whereNotNull('user_id');
                }
            }

            // Get filtered reports
            $reports = $query->orderBy('created_at', 'desc')->get();

            // Calculate statistics
            $totalReports = $reports->count();
            $pendingCount = $reports->where('status', 'pending')->count();
            $inProgressCount = $reports->where('status', 'in_progress')->count();
            $resolvedCount = $reports->where('status', 'resolved')->count();
            $deletedCount = $reports->filter(fn($report) => str_starts_with($report->status, 'Deleted'))->count();

            // Prepare data for PDF
            $pdfData = [
                'reports' => $reports,
                'totalReports' => $totalReports,
                'pendingCount' => $pendingCount,
                'inProgressCount' => $inProgressCount,
                'resolvedCount' => $resolvedCount,
                'deletedCount' => $deletedCount,
                'exportDate' => Carbon::now('Asia/Manila')->format('F d, Y g:i A'),
                'filters' => [
                    'search' => $request->search,
                    'status' => $request->status,
                    'userType' => $request->userType,
                ],
            ];

            // Generate PDF
            $pdf = PDF::loadView('admin.exports.reports-pdf', $pdfData)
                ->setPaper('a4', 'landscape')
                ->setOption('defaultFont', 'Arial');

            $filename = 'aquatrack-reports-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }
}
