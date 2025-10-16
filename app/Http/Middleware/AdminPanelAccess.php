<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si pas connecté ou pas admin → redirection vers login Filament
        if (!$user || (!$user->hasRole('admin') && !$user->hasRole('user'))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/admin/login');
        }
        return $next($request);
    }
}