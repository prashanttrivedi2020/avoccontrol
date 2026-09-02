<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.users', compact('users'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return redirect()->route('admin.users.index')->with('success', $user->name . ' has been ' . ($user->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function userLosses(User $user): View
    {
        $losses = Loss::with(['product', 'user'])
            ->where('user_id', $user->id)
            ->orderByDesc('loss_date')
            ->get();

        return view('admin.user-losses', compact('user', 'losses'));
    }
}
