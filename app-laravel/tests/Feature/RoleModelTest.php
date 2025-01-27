<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_users_for_a_role()
    {
        // Stwórz rolę
        $role = Role::factory()->create(['name' => 'user']);

        // Stwórz użytkowników przypisanych do tej roli
        $users = User::factory(2)->create(['role_id' => $role->id]);

        // Pobierz użytkowników przypisanych do roli
        $this->assertCount(2, $role->users);
        $this->assertEquals($users->pluck('id')->toArray(), $role->users->pluck('id')->toArray());
    }
}
