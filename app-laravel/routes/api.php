<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FriendController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\AdminController;

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
    Route::post('/register', [AuthController::class, 'register']); // Rejestracja
    Route::post('/login', [AuthController::class, 'login']);       // Logowanie
    Route::post('/logout', [AuthController::class, 'logout']);     // Wylogowanie
});

// Trasy dla użytkowników
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index']);            // Pobieranie listy użytkowników
    Route::get('/me', [UserController::class, 'profile']);        // Pobieranie profilu zalogowanego użytkownika
    Route::put('/me', [UserController::class, 'update']);         // Aktualizacja profilu
    Route::post('/me/profile-picture', [UserController::class, 'updateProfilePicture']); // Aktualizacja zdjęcia profilowego
});

// Trasy dla znajomych
Route::prefix('friends')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FriendController::class, 'index']);          // Pobieranie listy znajomych
    Route::post('/', [FriendController::class, 'store']);         // Dodawanie znajomego
    Route::delete('/{id}', [FriendController::class, 'destroy']); // Usuwanie znajomego
});

// Trasy dla notatek
Route::prefix('notes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NoteController::class, 'index']);            // Pobieranie listy notatek
    Route::post('/', [NoteController::class, 'store']);           // Tworzenie nowej notatki
    Route::put('/{id}', [NoteController::class, 'update']);       // Edycja notatki
    Route::delete('/{id}', [NoteController::class, 'destroy']);   // Usuwanie notatki
    Route::post('/{id}/share', [NoteController::class, 'share']); // Udostępnianie notatki
    Route::get('/shared', [NoteController::class, 'sharedNotes']); // Pobieranie współdzielonych notatek
});

// Trasy dla panelu administracyjnego
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'index']);      // Lista użytkowników
    Route::post('/users', [AdminController::class, 'store']);     // Dodawanie użytkownika
    Route::delete('/users/{id}', [AdminController::class, 'destroy']); // Usuwanie użytkownika
    Route::get('/sql-dump', [AdminController::class, 'sqlDump']); // Eksport bazy danych
    Route::post('/sql-import', [AdminController::class, 'sqlImport']); // Import bazy danych
});
