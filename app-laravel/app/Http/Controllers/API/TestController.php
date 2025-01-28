<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Test działa!'], 200);
    }
}
