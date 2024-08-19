<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Str;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Generate or retrieve the session ID
        $sessionId = $request->cookie('session_id') ?: Str::uuid()->toString();

        if (!$request->cookie('session_id')) {
            // Store the session ID in a cookie if it doesn't exist
            cookie()->queue(cookie('session_id', $sessionId, 60 * 24 * 7)); // Store for 7 days
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'sessionId' => $sessionId, // Share the session ID with Inertia
        ]);
    }
}
