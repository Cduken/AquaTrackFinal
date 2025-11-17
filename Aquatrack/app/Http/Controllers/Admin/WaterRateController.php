<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaterRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WaterRateController extends Controller
{
    public function index()
    {
        $waterRates = WaterRate::ordered()->get();

        return Inertia::render('Admin/WaterRates', [
            'waterRates' => $waterRates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_consumption' => 'required|integer|min:0',
            'max_consumption' => 'nullable|integer|min:0|gt:min_consumption',
            'rate_per_cubic' => 'required|numeric|min:0',
            'fixed_charge' => 'required|numeric|min:0',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        // Improved overlapping consumption ranges check
        $overlapping = WaterRate::where('id', '!=', $request->id ?? 0)
            ->where(function ($query) use ($validated) {
                // Check if new range starts within an existing range
                $query->where(function ($q) use ($validated) {
                    $q->where('min_consumption', '<=', $validated['min_consumption'])
                        ->where(function ($q2) use ($validated) {
                            $q2->where('max_consumption', '>=', $validated['min_consumption'])
                                ->orWhereNull('max_consumption');
                        });
                })
                    // Check if new range ends within an existing range
                    ->orWhere(function ($q) use ($validated) {
                        if ($validated['max_consumption'] !== null) {
                            $q->where('min_consumption', '<=', $validated['max_consumption'])
                                ->where(function ($q2) use ($validated) {
                                    $q2->where('max_consumption', '>=', $validated['max_consumption'])
                                        ->orWhereNull('max_consumption');
                                });
                        }
                    })
                    // Check if new range completely contains an existing range
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('min_consumption', '>=', $validated['min_consumption']);
                        if ($validated['max_consumption'] !== null) {
                            $q->where(function ($q2) use ($validated) {
                                $q2->where('max_consumption', '<=', $validated['max_consumption'])
                                    ->orWhereNull('max_consumption');
                            });
                        }
                    });
            })->exists();

        if ($overlapping) {
            return back()->withErrors([
                'min_consumption' => 'This consumption range overlaps with an existing rate tier.'
            ]);
        }

        WaterRate::create($validated);

        return redirect()->back()->with('success', 'Water rate created successfully.');
    }

    public function update(Request $request, WaterRate $waterRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_consumption' => 'required|integer|min:0',
            'max_consumption' => 'nullable|integer|min:0|gt:min_consumption',
            'rate_per_cubic' => 'required|numeric|min:0',
            'fixed_charge' => 'required|numeric|min:0',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        // Check for overlapping consumption ranges (excluding current rate)
        $overlapping = WaterRate::where('id', '!=', $waterRate->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('min_consumption', [$validated['min_consumption'], $validated['max_consumption'] ?? PHP_INT_MAX])
                    ->orWhereBetween('max_consumption', [$validated['min_consumption'], $validated['max_consumption'] ?? PHP_INT_MAX])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('min_consumption', '<=', $validated['min_consumption'])
                            ->where(function ($query) use ($validated) {
                                $query->where('max_consumption', '>=', $validated['min_consumption'])
                                    ->orWhereNull('max_consumption');
                            });
                    });
            })->exists();

        if ($overlapping) {
            return back()->withErrors([
                'min_consumption' => 'This consumption range overlaps with an existing rate tier.'
            ]);
        }

        $waterRate->update($validated);

        return redirect()->back()->with('success', 'Water rate updated successfully.');
    }



    // In WaterRateController - add this method or modify existing ones
    public function updateStatus(Request $request, WaterRate $waterRate)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        // Prevent deactivating the last active rate
        if (!$request->is_active) {
            $activeRatesCount = WaterRate::where('is_active', true)->count();
            if ($activeRatesCount <= 1) {
                return back()->withErrors([
                    'is_active' => 'Cannot deactivate the last active water rate.'
                ]);
            }
        }

        $waterRate->update([
            'is_active' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Water rate status updated successfully.');
    }

    public function destroy(WaterRate $waterRate)
    {
        // Prevent deleting the last active rate
        $activeRatesCount = WaterRate::where('is_active', true)->count();
        if ($waterRate->is_active && $activeRatesCount <= 1) {
            return back()->withErrors([
                'delete' => 'Cannot delete the last active water rate.'
            ]);
        }

        $waterRate->delete();

        return redirect()->back()->with('success', 'Water rate deleted successfully.');
    }
}
