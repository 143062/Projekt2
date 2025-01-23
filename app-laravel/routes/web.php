<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\FriendController;

// Trasy UserController
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::post('/update_profile_picture', [UserController::class, 'updateProfilePicture']);
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

// Trasy NoteController
Route::get('/dashboard', [NoteController::class, 'dashboard'])->name('note.dashboard');
Route::post('/add_note', [NoteController::class, 'addNote'])->name('note.add');
Route::get('/get_note', [NoteController::class, 'editNote'])->name('note.get');
Route::post('/delete_note', [NoteController::class, 'deleteNote'])->name('note.delete');

// Trasy AdminController
Route::get('/admin_panel', [AdminController::class, 'adminPanel'])->name('admin.panel');
Route::get('/admin/get_users', [AdminController::class, 'getUsers'])->name('admin.get_users');
Route::post('/admin/delete_user', [AdminController::class, 'deleteUser'])->name('admin.delete_user');

Route::post('/admin/add_user', [AdminController::class, 'addUser'])->name('admin.add_user');
Route::post('/admin/reset_password', [AdminController::class, 'resetPassword'])->name('admin.reset_password');
Route::post('/admin/sql_dump', [AdminController::class, 'sqlDump'])->name('admin.sql_dump');
Route::post('/admin/sql_import', [AdminController::class, 'sqlImport'])->name('admin.sql_import');

// Trasa LogoutController
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// Trasy FriendController
Route::get('/friends', [FriendController::class, 'friends'])->name('friends.list');
Route::post('/add-friend', [FriendController::class, 'addFriend'])->name('friends.add');
Route::post('/remove-friend', [FriendController::class, 'deleteFriend'])->name('friends.remove');

// Trasa do testowania połączenia z bazą danych
Route::get('/test-db', function () {
    try {
        $users = DB::table('users')->get();
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.db');

// Trasa do importu bazy danych
Route::match(['get', 'post'], '/import_database', function () {
    include base_path('resources/views/import_database.php');
})->name('database.import');

// Domyślna strona startowa
Route::get('/', fn() => redirect('/login'));
