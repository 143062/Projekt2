<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckUserRole
{
    /**
     * Obsługuje przychodzące żądanie.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles - lista ról, które mają dostęp
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pobranie zalogowanego użytkownika
        $user = $request->user();

        // Jeśli użytkownik nie jest zalogowany
        if (!$user) {
            Log::warning('[CheckUserRole] Próba dostępu bez logowania');
            return response()->json(['message' => 'Brak dostępu.'], 403);
        }

        // Upewnienie się, że relacja "role" jest załadowana
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Sprawdzenie, czy użytkownik ma rolę
        if (!$user->role || !in_array($user->role->name, $roles)) {
            Log::warning("[CheckUserRole] Użytkownik {$user->id} nie ma wymaganej roli", [
                'required_roles' => $roles,
                'user_role' => $user->role ? $user->role->name : 'Brak roli',
            ]);
            return response()->json(['message' => 'Brak dostępu.'], 403);
        }

        return $next($request);
    }
}
