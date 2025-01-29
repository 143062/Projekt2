<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllerAPI;
use App\Http\Controllers\AuthControllerAPI;
use App\Http\Controllers\FriendControllerAPI;
use App\Http\Controllers\NoteControllerAPI;
use App\Http\Controllers\TestControllerAPI;
use App\Http\Controllers\UserControllerAPI;

// Trasa testowa
Route::get('/ping', fn() => response()->json(['message' => 'API działa!']));

// Trasy dla autoryzacji
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthControllerAPI::class, 'register']);
    Route::post('/login', [AuthControllerAPI::class, 'login']);
    Route::delete('/logout', [AuthControllerAPI::class, 'logout']); // Poprawione na DELETE
});

// Trasy dla użytkowników
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserControllerAPI::class, 'index']);             // Pobieranie listy użytkowników
    Route::get('/me', [UserControllerAPI::class, 'getProfile']);      // Pobieranie profilu zalogowanego użytkownika
    Route::get('/dashboard', [UserControllerAPI::class, 'getDashboard']); // Wyświetlanie dashboardu użytkownika
    Route::put('/me', [UserControllerAPI::class, 'updateProfile']);   // Aktualizacja profilu
    Route::post('/me/profile-picture', [UserControllerAPI::class, 'updateProfilePicture']); // Aktualizacja zdjęcia profilowego
    
    // 🔗 Trasa do pobierania zdjęcia profilowego przez API
    Route::get('/me/profile-picture', [UserControllerAPI::class, 'getProfilePicture']);
});

// Trasy dla znajomych
Route::prefix('friends')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FriendControllerAPI::class, 'index']); // Pobieranie listy znajomych
    Route::post('/', [FriendControllerAPI::class, 'store']); // Dodanie znajomego
    Route::delete('/{id}', [FriendControllerAPI::class, 'destroy']); // Usuwanie znajomego
});

// Trasy dla notatek
Route::prefix('notes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NoteControllerAPI::class, 'index']); // Pobieranie listy notatek
    Route::post('/', [NoteControllerAPI::class, 'store']); // Tworzenie notatki
    Route::get('/{id}', [NoteControllerAPI::class, 'show']); // Pobieranie pojedynczej notatki
    Route::put('/{id}', [NoteControllerAPI::class, 'storeOrUpdate']); // Edycja notatki
    Route::delete('/{id}', [NoteControllerAPI::class, 'destroy']); // Usuwanie notatki
    Route::post('/{id}/share', [NoteControllerAPI::class, 'share']); // Udostępnianie notatki
    Route::get('/shared', [NoteControllerAPI::class, 'sharedNotes']);
    Route::get('/{id}/shared-users', [NoteControllerAPI::class, 'getSharedUsersByNoteId']);
});

// Trasy administracyjne
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminControllerAPI::class, 'getUsers']); 
    Route::post('/users', [AdminControllerAPI::class, 'addUser']);
    Route::delete('/users/{id}', [AdminControllerAPI::class, 'deleteUser']);
    Route::get('/sql-dump', [AdminControllerAPI::class, 'exportDatabase']);
    Route::post('/sql-import', [AdminControllerAPI::class, 'importDatabase']);
});

// Trasy testowe
Route::prefix('test')->group(function () {
    Route::get('/', [TestControllerAPI::class, 'index']);
});
