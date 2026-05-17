<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure user is authenticated and has admin role
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/not-authorized'); // Redirect if not an admin
        }

        return $next($request);
    }
}

