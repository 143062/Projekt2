<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Wypełnialne kolumny
    protected $fillable = ['name'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Timestampy
    public $timestamps = false;

    // Relacja: rola ma wielu użytkowników
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
