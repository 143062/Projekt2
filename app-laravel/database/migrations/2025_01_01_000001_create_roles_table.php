<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()')); // Automatyczne generowanie UUID
            $table->string('name', 100)->unique(); // Nazwa roli, unikalna
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
