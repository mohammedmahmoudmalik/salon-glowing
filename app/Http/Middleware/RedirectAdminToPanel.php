<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['admin', 'receptionist'])) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::check() && Auth::user()->hasRole('owner')) {
            return redirect()->route('admin.owner.dashboard');
        }

        return $next($request);
    }
}
