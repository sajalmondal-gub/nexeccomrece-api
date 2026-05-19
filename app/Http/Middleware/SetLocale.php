<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Detect locale from query parameter, Accept-Language header, or session
        $locale = $request->get('lang') 
            ?? $request->header('Accept-Language') 
            ?? ($request->hasSession() ? $request->session()->get('locale') : null)
            ?? config('app.locale');

        // Normalize / sanitize the locale
        if (!in_array($locale, ['en', 'bn'])) {
            $locale = 'en';
        }

        // 2. Set application locale
        app()->setLocale($locale);

        // 3. Store in session if session is available (helps the web admin panel persist it)
        if ($request->hasSession() && $request->get('lang')) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
