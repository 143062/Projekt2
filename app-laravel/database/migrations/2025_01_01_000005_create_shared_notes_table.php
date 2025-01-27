<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_notes', function (Blueprint $table) {
            $table->uuid('note_id'); // Klucz obcy do tabeli Notes
            $table->uuid('user_id'); // Klucz obcy do tabeli Users

            // Definicja kluczy obcych
            $table->foreign('note_id')
                  ->references('id')
                  ->on('notes')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Definicja klucza głównego dla pary note_id + user_id
            $table->primary(['note_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_notes');
    }
};
