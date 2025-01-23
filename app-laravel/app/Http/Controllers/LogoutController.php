<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout()
    {
        // Zniszczenie wszystkich zmiennych sesji
        Session::flush();

        // Przekierowanie na stronę logowania
        return redirect('/login');
    }
}
