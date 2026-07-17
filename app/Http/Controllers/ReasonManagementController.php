<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReasonManagementController extends Controller
{
    public function index()
    {
         $userIds = $this->getUserIds();
          //$reasons = Auth::user()->reasons()->withoutTrashed()->orderBy('name')->get();
        
          $reasons = Reason::whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->withoutTrashed()
            ->orderBy('name')
            ->get();

        return view('reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('reasons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:reasons,name,NULL,id,user_id,' . Auth::id(),
        ]);

        Auth::user()->reasons()->create([
            'name' => $validated['name'],
            'sort_order' => Auth::user()->reasons()->count() + 1,
            'is_active' => true,
        ]);

        return redirect()->route('reasons.index')->with('success', __('Reason created successfully.'));
    }

    public function edit(Reason $reason)
    {
        $this->authorize('update', $reason);

        return view('reasons.edit', compact('reason'));
    }

    public function update(Request $request, Reason $reason)
    {
        $this->authorize('update', $reason);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:reasons,name,' . $reason->id . ',id,user_id,' . Auth::id(),
            'is_active' => 'nullable|boolean',
        ]);

        $reason->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('reasons.index')->with('success', __('Reason updated successfully.'));
    }

    public function destroy(Reason $reason)
    {
        $this->authorize('delete', $reason);

        $reason->delete();

        return redirect()->route('reasons.index')->with('success', __('Reason deleted successfully.'));
    }
}
