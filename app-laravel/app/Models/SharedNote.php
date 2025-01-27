<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SharedNote extends Pivot
{
    // Tabela pośrednia, nie wymaga dodatkowych metod
    protected $table = 'shared_notes';
}
