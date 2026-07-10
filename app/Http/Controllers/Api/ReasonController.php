<?php

namespace App\Http\Controllers\Api;

use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ReasonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:reasons,name,NULL,id,user_id,' . Auth::id(),
        ]);

        try {
            $reason = Reason::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'sort_order' => Auth::user()->reasons()->count() + 1,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'reason' => $reason,
                'message' => 'Reason created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating reason: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActive()
    {
        Reason::ensureDefaultsForUser(Auth::user());

        $reasons = Auth::user()->reasons()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($reasons);
    }
}
