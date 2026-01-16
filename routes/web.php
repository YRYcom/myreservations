<?php

use App\Http\Controllers\BienPhotoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('/home');
});

Route::middleware('auth')->post('/reservations/display-finished', function (Request $request) {
    session(['display_finished' => $request->boolean('display_finished')]);
    return response()->json([
        'success' => true,
    ]);
})->name('reservations.display-finished');

// Route sécurisée pour les photos des biens
Route::middleware('auth')->get('/biens/{bien}/photo', [BienPhotoController::class, 'show'])
    ->name('bien.photo');
