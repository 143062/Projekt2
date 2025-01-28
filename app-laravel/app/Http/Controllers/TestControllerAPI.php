<?php

namespace App\Http\Controllers;

class TestControllerAPI extends Controller
{
    /**
     * Endpoint testowy API.
     */
    public function index()
    {
        return response()->json(['message' => 'Test działa!'], 200);
    }
}
