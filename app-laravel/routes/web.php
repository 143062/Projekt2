<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 🔹 Trasy logowania i rejestracji
Route::get('/login', fn() => view('login'))->name('login');
Route::get('/register', fn() => view('register'))->name('register');

// 🔹 Trasa do testowania połączenia z bazą danych
Route::get('/test-db', function () {
    try {
        $users = DB::table('users')->get();
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.db');

// 🔹 Domyślna strona startowa
Route::get('/', fn() => redirect('/login'));

// 🔹 Dashboard użytkownika
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

// 🔹 Strona administratora
Route::get('/admin_panel', fn() => view('admin_panel'))->name('admin_panel');

// 🔹 Profil użytkownika
Route::get('/profile', fn() => view('profile'))->name('profile');
