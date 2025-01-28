<?php

namespace Database\Factories;

use App\Models\SharedNote;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SharedNoteFactory extends Factory
{
    protected $model = SharedNote::class;

    public function definition()
    {
        $note = Note::inRandomOrder()->first() ?? Note::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        // Sprawdzenie unikalności przed zwróceniem danych
        while (SharedNote::where('note_id', $note->id)->where('user_id', $user->id)->exists()) {
            $note = Note::inRandomOrder()->first() ?? Note::factory()->create();
            $user = User::inRandomOrder()->first() ?? User::factory()->create();
        }

        return [
            'note_id' => $note->id,
            'user_id' => $user->id,
        ];
    }
}
