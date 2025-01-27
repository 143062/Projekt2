<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Wypełnialne kolumny
    protected $fillable = ['name'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Relacja: rola ma wielu użytkowników
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
