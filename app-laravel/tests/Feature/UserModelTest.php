<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_notes_for_a_user()
    {
        // Stwórz użytkownika
        $user = User::factory()->create();

        // Stwórz notatki przypisane do tego użytkownika
        $notes = Note::factory(3)->create(['user_id' => $user->id]);

        // Pobierz notatki użytkownika
        $this->assertCount(3, $user->notes);
        $this->assertEquals($notes->pluck('id')->toArray(), $user->notes->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_fetch_friends_for_a_user()
    {
        // Stwórz użytkownika
        $user = User::factory()->create();

        // Stwórz znajomych
        $friends = User::factory(2)->create();

        // Powiąż znajomych z użytkownikiem
        $user->friends()->attach($friends->pluck('id')->toArray());

        // Pobierz znajomych użytkownika
        $this->assertCount(2, $user->friends);
        $this->assertEquals($friends->pluck('id')->toArray(), $user->friends->pluck('id')->toArray());
    }
}
