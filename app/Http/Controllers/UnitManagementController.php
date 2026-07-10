<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitManagementController extends Controller
{
    public function index()
    {
        $units = Auth::user()->units()->withTrashed()->orderBy('name')->get();

        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,NULL,id,user_id,' . Auth::id(),
        ]);

        Auth::user()->units()->create([
            'name' => $validated['name'],
            'sort_order' => Auth::user()->units()->count() + 1,
            'is_active' => true,
        ]);

        return redirect()->route('units.index')->with('success', __('Unit created successfully.'));
    }

    public function edit(Unit $unit)
    {
        $this->authorize('update', $unit);

        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $unit->id . ',id,user_id,' . Auth::id(),
            'is_active' => 'nullable|boolean',
        ]);

        $unit->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('units.index')->with('success', __('Unit updated successfully.'));
    }

    public function destroy(Unit $unit)
    {
        $this->authorize('delete', $unit);

        $unit->delete();

        return redirect()->route('units.index')->with('success', __('Unit deleted successfully.'));
    }
}
