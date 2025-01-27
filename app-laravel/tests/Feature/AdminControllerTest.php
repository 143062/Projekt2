<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test dostępu do listy użytkowników.
     */
    public function test_admin_can_view_users()
    {
        // Tworzenie użytkownika admina
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Logowanie jako admin
        $this->actingAs($admin);

        // Wykonanie żądania
        $response = $this->getJson('/api/admin/users');

        // Sprawdzenie odpowiedzi
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'login', 'email', 'role_id']
            ]
        ]);
    }

    /**
     * Test dodawania nowego użytkownika.
     */
    public function test_admin_can_add_user()
    {
        // Tworzenie użytkownika admina
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Logowanie jako admin
        $this->actingAs($admin);

        // Wykonanie żądania
        $response = $this->postJson('/api/admin/users', [
            'email' => 'newuser@example.com',
            'login' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        // Sprawdzenie odpowiedzi
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /**
     * Test usuwania użytkownika.
     */
    public function test_admin_can_delete_user()
    {
        // Tworzenie użytkownika admina i zwykłego użytkownika
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $userRole = Role::factory()->create(['name' => 'user']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        // Logowanie jako admin
        $this->actingAs($admin);

        // Wykonanie żądania
        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        // Sprawdzenie odpowiedzi
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
