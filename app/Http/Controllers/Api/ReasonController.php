<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        //Reason::ensureDefaultsForUser(Auth::user());
        $userIds = $this->getUserIds();

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            Reason::ensureDefaultsForUser($user);
        }

        // $reasons = Auth::user()->reasons()
        //     ->where('is_active', true)
        //     ->orderBy('sort_order')
        //     ->orderBy('name')
        //     ->get(['id', 'name']);

        $reasons = Reason::whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($reasons);
    }
}
