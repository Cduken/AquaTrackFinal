<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AnnouncementsController extends Controller
{
    // Use the exact same zones structure as AdminUsersController
    protected $zones = [
        "Zone 1" => ["Poblacion Sur"],
        "Zone 2" => ["Poblacion Centro"],
        "Zone 3" => ["Poblacion Centro"],
        "Zone 4" => ["Poblacion Norte"],
        "Zone 5" => ["Candajec", "Buangan"],
        "Zone 6" => ["Bonbon"],
        "Zone 7" => ["Bonbon"],
        "Zone 8" => ["Nahawan"],
        "Zone 9" => ["Caboy", "Villaflor", "Cantuyoc"],
        "Zone 10" => ["Bacani", "Mataub", "Comaang", "Tangaran"],
        "Zone 11" => ["Cantuyoc", "Nahawan"],
        "Zone 12" => ["Lajog", "Buacao"],
    ];

    // Add this method to get all barangays for validation
    protected function getAllBarangays()
    {
        $allBarangays = [];
        foreach ($this->zones as $zoneBarangays) {
            $allBarangays = array_merge($allBarangays, $zoneBarangays);
        }
        return array_unique($allBarangays);
    }

    public function customerIndex()
    {
        $user = Auth::user();

        if (!$user) {
            return Inertia::render('Customer/Announcements', [
                'announcements' => [],
            ]);
        }

        $announcements = Announcements::where('status', 'active')
            ->where(function ($query) use ($user) {
                // Global announcements (for everyone)
                $query->where(function ($q) {
                    $q->where('zone', 'All Zones')
                        ->where('barangay', 'All Barangays');
                });

                // If user has zone and barangay set, show announcements for their specific location
                if (!empty($user->zone) && !empty($user->barangay)) {
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('zone', $user->zone)
                            ->where('barangay', $user->barangay);
                    });
                }

                // If user has zone but no barangay, show announcements for their zone (all barangays)
                if (!empty($user->zone)) {
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('zone', $user->zone)
                            ->where('barangay', 'All Barangays');
                    });
                }

                // If user has barangay but no zone (unlikely), show announcements for their barangay (all zones)
                if (!empty($user->barangay)) {
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('zone', 'All Zones')
                            ->where('barangay', $user->barangay);
                    });
                }
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'status' => ucfirst($announcement->status),
                    'start_date' => $announcement->start_date?->format('Y-m-d'),
                    'end_date' => $announcement->end_date?->format('Y-m-d'),
                    'created_at' => $announcement->created_at->toISOString(),
                    'updated_at' => $announcement->updated_at->toISOString(),
                    'author' => $announcement->author ?? 'ClarinWaterDistrict',
                    'zone' => $announcement->zone,
                    'barangay' => $announcement->barangay,
                    'scope' => $this->getAnnouncementScope($announcement),
                ];
            });

        return Inertia::render('Customer/Announcements', [
            'announcements' => $announcements,
            'userZone' => $user->zone,
            'userBarangay' => $user->barangay,
        ]);
    }



    public function guestIndex()
    {
        // First, let's see what's actually in the database
        $allActiveAnnouncements = Announcements::where('status', 'active')->get();

        Log::info('DEBUG: All active announcements in database', [
            'total' => $allActiveAnnouncements->count(),
            'announcements' => $allActiveAnnouncements->map(function ($ann) {
                return [
                    'id' => $ann->id,
                    'title' => $ann->title,
                    'zone' => $ann->zone,
                    'barangay' => $ann->barangay,
                    'status' => $ann->status,
                ];
            })->toArray()
        ]);

        // For guests, show announcements that are for everyone (global announcements)
        $announcements = Announcements::where('status', 'active')
            ->where(function ($query) {
                // Global announcements - either both are "All" or both are null/empty
                $query->where(function ($q) {
                    $q->where('zone', 'All Zones')
                        ->where('barangay', 'All Barangays');
                })->orWhere(function ($q) {
                    // Also include announcements where zone and barangay are empty/null
                    $q->whereNull('zone')
                        ->whereNull('barangay');
                })->orWhere(function ($q) {
                    // Or where zone is empty and barangay is empty
                    $q->where('zone', '')
                        ->where('barangay', '');
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        Log::info('DEBUG: Filtered announcements for guest', [
            'total' => $announcements->count(),
            'announcements' => $announcements->map(function ($ann) {
                return [
                    'id' => $ann->id,
                    'title' => $ann->title,
                    'zone' => $ann->zone,
                    'barangay' => $ann->barangay,
                ];
            })->toArray()
        ]);

        $formattedAnnouncements = $announcements->map(function ($announcement) {
            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'status' => ucfirst($announcement->status),
                'start_date' => $announcement->start_date?->format('Y-m-d'),
                'end_date' => $announcement->end_date?->format('Y-m-d'),
                'created_at' => $announcement->created_at->toISOString(),
                'updated_at' => $announcement->updated_at->toISOString(),
                'author' => $announcement->author ?? 'ClarinWaterDistrict',
                'zone' => $announcement->zone,
                'barangay' => $announcement->barangay,
                'scope' => 'For Everyone',
            ];
        });

        return Inertia::render('Announcement/Announcements', [
            'announcements' => $formattedAnnouncements,
            'title' => 'Announcements - Clarin Water District',
            'description' => 'Latest updates and important information from Clarin Water District'
        ]);
    }

    public function index(Request $request)
    {
        Log::info('Announcements filters received:', $request->all());

        // Build query with filters
        $query = Announcements::query();

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where('status', $status);
        }

        // Apply zone filter
        if ($request->filled('zone') && $request->zone !== 'all') {
            $zone = $request->zone;
            $query->where('zone', $zone);
        }

        // Apply barangay filter
        if ($request->filled('barangay') && $request->barangay !== 'all') {
            $barangay = $request->barangay;
            $query->where('barangay', $barangay);
        }

        // Apply sorting
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['id', 'title', 'start_date', 'end_date', 'status', 'created_at', 'updated_at', 'zone', 'barangay'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'id';
        }

        $query->orderBy($sort, $order);

        // Get paginated results
        $perPage = $request->get('per_page', 10);
        $announcements = $query->paginate($perPage)->withQueryString();

        // Transform the data with proper display values
        $announcements->getCollection()->transform(function ($announcement) {
            // Determine zone display value
            $zoneDisplay = $announcement->zone ?? 'All Zones';

            // Determine barangay display value - FIXED LOGIC
            if (!empty($announcement->barangay)) {
                // If barangay has a value, use it
                $barangayDisplay = $announcement->barangay;
            } else if (!empty($announcement->zone)) {
                // If zone has value but barangay is empty/null, show "All Barangays"
                $barangayDisplay = 'All Barangays';
            } else {
                // If both zone and barangay are empty/null, show "All Barangays" for barangay
                $barangayDisplay = 'All Barangays';
            }

            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'date' => $announcement->created_at->format('Y-m-d'),
                'status' => ucfirst($announcement->status),
                'start_date' => $announcement->start_date?->format('Y-m-d'),
                'end_date' => $announcement->end_date?->format('Y-m-d'),
                'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $announcement->updated_at->format('Y-m-d H:i:s'),
                'zone' => $announcement->zone,
                'barangay' => $announcement->barangay,
                'zone_display' => $zoneDisplay,
                'barangay_display' => $barangayDisplay,
                'scope' => $this->getAnnouncementScope($announcement),
            ];
        });

        Log::info('Announcements query results:', [
            'total' => $announcements->total(),
            'per_page' => $announcements->perPage(),
            'current_page' => $announcements->currentPage(),
            'last_page' => $announcements->lastPage(),
            'filters_applied' => $request->only(['search', 'status', 'sort', 'order', 'per_page', 'zone', 'barangay']),
        ]);

        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements,
            'filters' => $request->only(['search', 'status', 'sort', 'order', 'per_page', 'zone', 'barangay']),
            'zones' => $this->zones,
        ]);
    }

    public function store(Request $request)
    {
        $allBarangays = $this->getAllBarangays();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'zone' => 'nullable|string',
            'barangay' => 'nullable|string',
        ]);

        // Ensure global announcements are consistently stored
        if (empty($request->zone) || $request->zone === 'All Zones' || $request->zone === '') {
            $validated['zone'] = 'All Zones';
            $validated['barangay'] = 'All Barangays';
        }
        // If specific zone selected but no barangay, set to "All Barangays" for that zone
        elseif (!empty($request->zone) && (empty($request->barangay) || $request->barangay === 'All Barangays')) {
            $validated['zone'] = $request->zone;
            $validated['barangay'] = 'All Barangays';
        }
        // If specific zone and barangay selected
        else {
            $validated['zone'] = $request->zone;
            $validated['barangay'] = $request->barangay;
        }

        Announcements::create($validated);

        return redirect()->back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, Announcements $announcement)
    {
        $allBarangays = $this->getAllBarangays();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'zone' => 'nullable|string',
            'barangay' => 'nullable|string',
        ]);

        // Handle zone and barangay assignment - FIXED LOGIC
        $zone = $request->zone;
        $barangay = $request->barangay;

        if (empty($zone) || $zone === '' || $zone === 'null') {
            $validated['zone'] = 'All Zones';
            $validated['barangay'] = 'All Barangays';
        } else {
            $validated['zone'] = $zone;

            // Only set to "All Barangays" if explicitly selected or empty
            if ($barangay === 'All Barangays' || empty($barangay) || $barangay === '' || $barangay === 'null') {
                $validated['barangay'] = 'All Barangays';
            } else {
                // Use the specific barangay that was selected
                $validated['barangay'] = $barangay;
            }
        }

        $announcement->update($validated);

        return redirect()->back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcements $announcement)
    {
        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    // Helper method to get announcement scope for display
    // Helper method to get announcement scope for display
    private function getAnnouncementScope($announcement)
    {
        if ($announcement->zone === 'All Zones' && $announcement->barangay === 'All Barangays') {
            return 'For Everyone';
        } elseif ($announcement->zone !== 'All Zones' && $announcement->barangay === 'All Barangays') {
            return "For {$announcement->zone} (All Barangays)";
        } else {
            return "For {$announcement->zone} - {$announcement->barangay}";
        }
    }

    // Helper method to get barangays by zone
    public function getBarangaysByZone($zone)
    {
        return response()->json([
            'barangays' => $this->zones[$zone] ?? []
        ]);
    }
}
