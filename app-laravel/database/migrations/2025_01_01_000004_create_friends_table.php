<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->uuid('user_id'); // Klucz obcy do tabeli Users
            $table->uuid('friend_id'); // Klucz obcy do tabeli Users (dla znajomego)

            // Definicja kluczy obcych
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('friend_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Definicja klucza głównego dla pary user_id + friend_id
            $table->primary(['user_id', 'friend_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
