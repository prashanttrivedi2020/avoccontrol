<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function getUserIds(): Collection
    {
        return User::whereIn('username', [
                'demo',
                'patron_vc',
            ])
            ->pluck('id')
            ->push(Auth::id())
            ->unique()
            ->values();
    }
}

