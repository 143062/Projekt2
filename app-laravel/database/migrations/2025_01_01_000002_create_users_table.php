<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()')); // UUID jako klucz główny z automatycznym generowaniem
            $table->string('login', 100)->unique(); // Login użytkownika, unikalny
            $table->string('email', 255)->unique(); // Adres e-mail, unikalny
            $table->string('password', 255); // Hasło użytkownika
            $table->string('profile_picture', 255)
                  ->default('img/profile/default/default_profile_picture.jpg'); // Domyślne zdjęcie profilowe
            $table->uuid('role_id')->nullable(); // Klucz obcy do tabeli Roles
            $table->timestamps(); // Automatyczne kolumny "created_at" i "updated_at"

            // Definicja klucza obcego dla role_id
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
