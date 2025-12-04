<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->post('/reservations/display-finished', function (Request $request) {
    session(['display_finished' => $request->boolean('display_finished')]);
    return response()->json([
        'success' => true,
    ]);
})->name('reservations.display-finished');
