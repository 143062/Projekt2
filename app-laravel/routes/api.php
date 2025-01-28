<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllerAPI;
use App\Http\Controllers\AuthControllerAPI;
use App\Http\Controllers\FriendControllerAPI;
use App\Http\Controllers\NoteControllerAPI;
use App\Http\Controllers\TestControllerAPI;
use App\Http\Controllers\UserControllerAPI;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the ServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Trasa testowa, która nie wymaga autoryzacji
Route::get('/ping', function () {
    return response()->json(['message' => 'API działa!']);
});

// Trasa wymagająca autoryzacji przez Sanctum
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Trasy dla autoryzacji
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthControllerAPI::class, 'register']); // Rejestracja
    Route::post('/login', [AuthControllerAPI::class, 'login']);       // Logowanie
    Route::post('/logout', [AuthControllerAPI::class, 'logout']);     // Wylogowanie
});

// Trasy dla użytkowników
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserControllerAPI::class, 'index']);             // Pobieranie listy użytkowników
    Route::get('/me', [UserControllerAPI::class, 'getProfile']);      // Pobieranie profilu zalogowanego użytkownika
    Route::get('/dashboard', [UserControllerAPI::class, 'getDashboard']); // Wyświetlanie dashboardu użytkownika
    Route::put('/me', [UserControllerAPI::class, 'updateProfile']);   // Aktualizacja profilu
    Route::post('/me/profile-picture', [UserControllerAPI::class, 'updateProfilePicture']); // Aktualizacja zdjęcia profilowego
});

// Trasy dla znajomych
Route::prefix('friends')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FriendControllerAPI::class, 'index']);          // Pobieranie listy znajomych
    Route::post('/', [FriendControllerAPI::class, 'store']);         // Dodawanie znajomego
    Route::delete('/{id}', [FriendControllerAPI::class, 'destroy']); // Usuwanie znajomego
});

// Trasy dla notatek
Route::prefix('notes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NoteControllerAPI::class, 'index']);            // Pobieranie listy notatek
    Route::post('/', [NoteControllerAPI::class, 'storeOrUpdate']);   // Tworzenie notatki
    Route::put('/{id}', [NoteControllerAPI::class, 'storeOrUpdate']); // Edycja notatki
    Route::delete('/{id}', [NoteControllerAPI::class, 'destroy']);   // Usuwanie notatki
    Route::post('/{id}/share', [NoteControllerAPI::class, 'share']); // Udostępnianie notatki
    Route::get('/shared', [NoteControllerAPI::class, 'sharedNotes']); // Pobieranie współdzielonych notatek
    Route::get('/{id}/shared-users', [NoteControllerAPI::class, 'getSharedUsersByNoteId']); // Pobieranie użytkowników, którym udostępniono notatkę
});

// Trasy dla panelu administracyjnego
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminControllerAPI::class, 'getUsers']);      // Lista użytkowników
    Route::post('/users', [AdminControllerAPI::class, 'addUser']);      // Dodawanie użytkownika
    Route::delete('/users/{id}', [AdminControllerAPI::class, 'deleteUser']); // Usuwanie użytkownika
    Route::get('/sql-dump', [AdminControllerAPI::class, 'exportDatabase']); // Eksport bazy danych
    Route::post('/sql-import', [AdminControllerAPI::class, 'importDatabase']); // Import bazy danych
});

// Trasy testowe
Route::prefix('test')->group(function () {
    Route::get('/', [TestControllerAPI::class, 'index']); // Trasa do testowego kontrolera
});
