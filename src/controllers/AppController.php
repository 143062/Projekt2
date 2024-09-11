<?php

namespace App\Controllers;

class AppController
{
    public function run($isTesting = false)
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($uri === '/' || $uri === '/login') {
            $controller = new UserController($isTesting);
        } elseif ($uri === '/register') {
            $controller = new UserController($isTesting);
            $controller->register();
        } elseif ($uri === '/add-note') {
            $controller = new NoteController($isTesting);
            $controller->addNote();
        } elseif ($uri === '/profile') {
            $controller = new UserController($isTesting);
            $controller->profile();
        } elseif ($uri === '/admin') {
            $controller = new AdminController($isTesting);
            $controller->adminPanel();
        } else {
            echo "Page not found.";
        }
    }
}
