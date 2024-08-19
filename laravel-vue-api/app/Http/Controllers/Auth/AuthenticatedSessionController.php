<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate the session to prevent fixation
        $request->session()->regenerate();

        // Transfer session cart to user cart after login
        $cartController = new CartController();
        $cartController->transferSessionCartToUser($request->user()->id);

        // Redirect to intended page or homepage
        return redirect()->intended('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Log out the user
        Auth::guard('web')->logout();

        // Invalidate and regenerate the session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to homepage
        return redirect('/');
    }
}
