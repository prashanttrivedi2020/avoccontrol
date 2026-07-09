<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['en', 'de', 'tr'];
    public const DEFAULT   = 'de';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', self::DEFAULT);

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
