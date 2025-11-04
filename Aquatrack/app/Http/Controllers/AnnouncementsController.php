<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class AnnouncementsController extends Controller
{

       public function customerIndex()
    {
        $announcements = Announcements::where('status', 'active')
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
                    'updated_at' => $announcement->updated_at->toISOString(), // This is crucial
                    'author' => $announcement->author ?? 'ClarinWaterDistrict',
                ];
            });

        return Inertia::render('Customer/Announcements', [
            'announcements' => $announcements,
        ]);
    }

    public function index(Request $request)
    {
        // Debug: Log the incoming filters
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
            Log::info('Applied search filter:', ['search' => $searchTerm]);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where('status', $status);
            Log::info('Applied status filter:', ['status' => $status]);
        }

        // Apply sorting
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['id', 'title', 'start_date', 'end_date', 'status', 'created_at', 'updated_at'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'id';
        }

        $query->orderBy($sort, $order);

        // Get paginated results
        $perPage = $request->get('per_page', 10);
        $announcements = $query->paginate($perPage)->withQueryString();

        // Transform the data
        $announcements->getCollection()->transform(function ($announcement) {
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
            ];
        });

        // Debug: Log the query results
        Log::info('Announcements query results:', [
            'total' => $announcements->total(),
            'per_page' => $announcements->perPage(),
            'current_page' => $announcements->currentPage(),
            'last_page' => $announcements->lastPage(),
            'filters_applied' => $request->only(['search', 'status', 'sort', 'order', 'per_page']),
        ]);

        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements,
            'filters' => $request->only(['search', 'status', 'sort', 'order', 'per_page']),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Announcements::create($validated);

        return redirect()->back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, Announcements $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $announcement->update($validated);

        return redirect()->back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcements $announcement)
    {
        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }
}
