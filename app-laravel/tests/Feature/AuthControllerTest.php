<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // Automatycznie resetuje bazę danych po każdym teście.

    /**
     * Test rejestracji użytkownika.
     */
    public function test_user_can_register(): void
    {
        // Przygotowanie danych wejściowych
        $userData = [
            'login' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Wysłanie żądania POST na trasę rejestracji
        $response = $this->postJson('/api/auth/register', $userData);

        // Weryfikacja odpowiedzi
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'login',
                         'email',
                         'created_at',
                         'updated_at',
                     ],
                     'token',
                 ]);

        // Weryfikacja czy użytkownik został dodany do bazy danych
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'login' => $userData['login'],
        ]);
    }
}
