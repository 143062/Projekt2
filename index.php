<?php

session_start();

require 'Routing.php';
require 'src/controllers/AppController.php';
require 'src/controllers/UserController.php';
require 'src/controllers/NoteController.php';
require 'src/controllers/AdminController.php';
require 'src/controllers/LogoutController.php';
require 'src/controllers/FriendController.php';
require 'src/repositories/UserRepository.php';
require 'src/repositories/NoteRepository.php';
require 'src/repositories/FriendRepository.php';

use App\Controllers\UserController;
use App\Controllers\NoteController;
use App\Controllers\AdminController;
use App\Controllers\LogoutController;
use App\Controllers\FriendController;


$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? null;
if ($path === '' || $path === 'index' || $path === 'index.php') {
    if ($isLoggedIn) {
        if ($userRole === 'admin') {
            $path = 'admin_panel';
        } else {
            $path = 'dashboard';
        }
    } else {
        $path = 'login';
    }
}

Router::get('', [UserController::class, 'login']);
Router::get('login', [UserController::class, 'login']);
Router::post('login', [UserController::class, 'login']);
Router::get('register', [UserController::class, 'register']);
Router::post('register', [UserController::class, 'register']);
Router::get('add_note', [NoteController::class, 'addNote']);
Router::post('add_note', [NoteController::class, 'addNote']);
Router::get('get_note', [NoteController::class, 'editNote']);
Router::post('delete_note', [NoteController::class, 'deleteNote']);
Router::get('profile', [UserController::class, 'profile']);
Router::post('update_profile_picture', [UserController::class, 'updateProfilePicture']);
Router::get('admin_panel', [AdminController::class, 'adminPanel']);
Router::post('admin/delete_user', [AdminController::class, 'deleteUser']);
Router::post('admin/add_user', [AdminController::class, 'addUser']);
Router::post('admin/reset_password', [AdminController::class, 'resetPassword']);
Router::get('admin/get_users', [AdminController::class, 'getUsers']);
Router::post('admin/sql_dump', [AdminController::class, 'sqlDump']);
Router::post('admin/sql_import', [AdminController::class, 'sqlImport']);
Router::get('dashboard', [UserController::class, 'dashboard']);
Router::get('friends', [FriendController::class, 'friends']);
Router::post('add-friend', [FriendController::class, 'addFriend']);
Router::post('remove-friend', [FriendController::class, 'deleteFriend']);
Router::get('logout', [LogoutController::class, 'logout']);


Router::get('import_database', fn() => require_once __DIR__ . '/Database/import_database.php');


Router::run($path);
