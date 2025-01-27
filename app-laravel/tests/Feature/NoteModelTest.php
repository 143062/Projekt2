<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_owner_of_a_note()
    {
        // Stwórz użytkownika
        $user = User::factory()->create();

        // Stwórz notatkę przypisaną do użytkownika
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Sprawdź, czy właściciel notatki jest poprawny
        $this->assertEquals($user->id, $note->user->id);
    }

    /** @test */
    public function it_can_fetch_users_shared_with()
    {
        // Stwórz użytkownika
        $user = User::factory()->create();

        // Stwórz notatkę przypisaną do użytkownika
        $note = Note::factory()->create(['user_id' => $user->id]);

        // Stwórz użytkowników, którym udostępniono notatkę
        $sharedUsers = User::factory(2)->create();
        $note->sharedWith()->attach($sharedUsers->pluck('id')->toArray());

        // Pobierz użytkowników, którym notatka została udostępniona
        $this->assertCount(2, $note->sharedWith);
        $this->assertEquals($sharedUsers->pluck('id')->toArray(), $note->sharedWith->pluck('id')->toArray());
    }
}
