<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker; // Resetuje bazę danych i pozwala na generowanie danych testowych.

    /**
     * Test pobierania listy użytkowników.
     */
    public function test_user_can_fetch_users_list(): void
    {
        // Tworzymy kilku użytkowników za pomocą fabryki
        User::factory()->count(5)->create();

        // Wykonujemy żądanie GET na trasę /api/users
        $response = $this->getJson('/api/users');

        // Weryfikujemy odpowiedź
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'login', 'email', 'created_at', 'updated_at'],
                 ]);
    }

    /**
     * Test pobierania profilu zalogowanego użytkownika.
     */
    public function test_user_can_fetch_their_profile(): void
    {
        // Tworzymy użytkownika i logujemy go
        $user = User::factory()->create();
        $this->actingAs($user); // Symulujemy zalogowanie użytkownika

        // Wykonujemy żądanie GET na trasę /api/users/me
        $response = $this->getJson('/api/users/me');

        // Weryfikujemy odpowiedź
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'login' => $user->login,
                     'email' => $user->email,
                 ]);
    }

    /**
     * Test aktualizacji profilu.
     */
    public function test_user_can_update_their_profile(): void
    {
        // Tworzymy użytkownika i logujemy go
        $user = User::factory()->create();
        $this->actingAs($user);

        // Dane do aktualizacji
        $updatedData = [
            'login' => 'UpdatedLogin',
            'email' => 'updated@example.com',
        ];

        // Wykonujemy żądanie PUT na trasę /api/users/me
        $response = $this->putJson('/api/users/me', $updatedData);

        // Weryfikujemy odpowiedź
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Profil został zaktualizowany.',
                     'user' => [
                         'login' => $updatedData['login'],
                         'email' => $updatedData['email'],
                     ],
                 ]);

        // Weryfikujemy, że dane w bazie zostały zaktualizowane
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'login' => $updatedData['login'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test aktualizacji zdjęcia profilowego.
     */
    public function test_user_can_update_profile_picture(): void
    {
        // Tworzymy użytkownika i logujemy go
        $user = User::factory()->create();
        $this->actingAs($user);

        // Przesyłamy plik obrazu
        $response = $this->postJson('/api/users/me/profile-picture', [
            'profile_picture' => \Illuminate\Http\UploadedFile::fake()->image('profile.jpg'),
        ]);

        // Weryfikujemy odpowiedź
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'newProfilePictureUrl',
                 ]);

        // Weryfikujemy, że ścieżka pliku została zaktualizowana w bazie danych
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'profile_picture' => 'img/profile/' . $user->id . '/profile.jpg',
        ]);
    }
}
