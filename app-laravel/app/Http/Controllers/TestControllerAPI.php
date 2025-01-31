<?php

namespace App\Http\Controllers;

class TestControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/test",
     *     summary="Endpoint testowy API",
     *     tags={"Test"},
     *     @OA\Response(response=200, description="Test działa poprawnie")
     * )
     */
    public function index()
    {
        return response()->json(['message' => 'Test działa!'], 200);
    }
}
