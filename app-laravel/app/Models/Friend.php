<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Friend extends Pivot
{
    use HasFactory;

    // Nazwa tabeli pośredniej
    protected $table = 'friends';

    // Wyłączanie automatycznego klucza głównego (pivot table nie ma 'id')
    public $incrementing = false;
    protected $primaryKey = null;

    // Wyłączanie automatycznych timestampów
    public $timestamps = false;

    /**
     * Relacja do użytkownika (opcjonalna)
     * Możemy zdefiniować relacje w modelu, jeśli jest to potrzebne.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
