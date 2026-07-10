<?php

namespace App\Http\Controllers\Api;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Create a new unit for the authenticated user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,NULL,id,user_id,' . Auth::id(),
        ]);

        try {
            $unit = Unit::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'sort_order' => Auth::user()->units()->count() + 1,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'unit' => $unit,
                'message' => 'Unit created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating unit: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all active units for the authenticated user
     */
    public function getActive()
    {
        $units = Auth::user()->units()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($units);
    }
}
