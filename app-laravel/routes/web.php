<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogoutController;

// Trasy logowania i rejestracji
Route::get('/login', fn() => view('login'))->name('login');
Route::get('/register', fn() => view('register'))->name('register');

// Trasa wylogowania
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// Trasa do testowania połączenia z bazą danych
Route::get('/test-db', function () {
    try {
        $users = DB::table('users')->get();
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.db');

// Domyślna strona startowa
Route::get('/', fn() => redirect('/login'));
