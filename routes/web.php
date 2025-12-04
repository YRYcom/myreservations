<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->post('/reservations/display-finished', function (Request $request) {
    session(['display_finished' => $request->boolean('display_finished')]);

    // Valider que l'URL de redirection est locale et sécurisée
    $redirectTo = $request->input('redirect_to');
    $validatedUrl = null;
    
    if ($redirectTo) {
        // Vérifier que l'URL commence par l'URL de base de l'application
        $appUrl = config('app.url');
        $parsedRedirect = parse_url($redirectTo);
        $parsedApp = parse_url($appUrl);
        
        // Autoriser seulement les URLs du même domaine
        if (isset($parsedRedirect['host']) && isset($parsedApp['host'])) {
            if ($parsedRedirect['host'] === $parsedApp['host']) {
                $validatedUrl = $redirectTo;
            }
        } elseif (!isset($parsedRedirect['host'])) {
            // URL relative, c'est sûr
            $validatedUrl = $redirectTo;
        }
    }

    return response()->json([
        'success' => true,
        'redirect_to' => $validatedUrl,
    ]);
})->name('reservations.display-finished');
