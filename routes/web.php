<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomePage;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomePage::class, 'index'])->name('home');
});


// use App\Http\Controllers\HomePage;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\LoginController;
// use App\Http\Controllers\Auth\RegisterController;

// // Page de login par défaut
// Route::get('/', [LoginController::class, 'showLoginForm'])->name('login'); 
// Route::get('/login', [LoginController::class, 'showLoginForm']); 
// Route::post('/login', [LoginController::class, 'login']);

// // Inscription
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);

// // Pages protégées par l'authentification
// Route::middleware('auth')->group(function () {
//     Route::get('/home', [HomePage::class, 'index'])->name('home');
// });
