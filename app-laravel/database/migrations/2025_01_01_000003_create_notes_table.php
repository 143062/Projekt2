<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()')); // UUID jako klucz główny z automatycznym generowaniem
            $table->uuid('user_id'); // Klucz obcy do tabeli Users
            $table->string('title', 255); // Tytuł notatki
            $table->text('content'); // Treść notatki
            $table->timestamps(); // Automatyczne kolumny "created_at" i "updated_at"

            // Definicja klucza obcego dla user_id
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
