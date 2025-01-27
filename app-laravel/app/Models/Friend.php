<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Friend extends Pivot
{
    // Tabela pośrednia, nie wymaga dodatkowych metod
    protected $table = 'friends';
}
