<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('/home');
});

Route::middleware('auth')->post('/reservations/display-finished', function (Request $request) {
    session(['display_finished' => $request->boolean('display_finished')]);
    return response()->json([
        'success' => true,
    ]);
})->name('reservations.display-finished');
