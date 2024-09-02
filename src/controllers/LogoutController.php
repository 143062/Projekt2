<?php

namespace App\Controllers;

class LogoutController
{
    public function logout()
    {
        session_start();

        // Zniszczenie wszystkich zmiennych sesji
        session_unset();

        // Zniszczenie sesji
        session_destroy();

        // Przekierowanie na stronę logowania
        header('Location: /login');
        exit();
    }
}
