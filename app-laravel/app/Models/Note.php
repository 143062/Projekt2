<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    // Wypełnialne kolumny
    protected $fillable = ['user_id', 'title', 'content'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Timestampy
    public $timestamps = true;

    // Relacja: notatka należy do użytkownika
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacja: notatka została udostępniona wielu użytkownikom
    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'shared_notes', 'note_id', 'user_id');
    }
}
