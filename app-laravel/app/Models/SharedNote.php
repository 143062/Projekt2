<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SharedNote extends Pivot
{
    use HasFactory;

    // Nazwa tabeli pośredniej
    protected $table = 'shared_notes';

    // Wyłączanie automatycznego klucza głównego
    public $incrementing = false;
    protected $primaryKey = null;

    // Wyłączanie automatycznych timestampów
    public $timestamps = false;
}
