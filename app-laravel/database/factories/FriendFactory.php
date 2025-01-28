<?php

namespace Database\Factories;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendFactory extends Factory
{
    protected $model = Friend::class;

    public function definition()
    {
        do {
            $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
            $friendId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

            // Sprawdzenie, czy relacja już istnieje
            $exists = Friend::where('user_id', $userId)
                            ->where('friend_id', $friendId)
                            ->exists();
        } while ($exists || $userId === $friendId); // Upewniamy się, że nie ma duplikatów i użytkownik nie jest swoim znajomym

        return [
            'user_id' => $userId,
            'friend_id' => $friendId,
        ];
    }
}
