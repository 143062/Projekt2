<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory;

    // Wypełnialne kolumny
    protected $fillable = ['id', 'user_id', 'title', 'content'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Timestampy
    public $timestamps = true;

    /**
     * Automatyczne generowanie UUID przy tworzeniu nowej notatki.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($note) {
            if (empty($note->{$note->getKeyName()})) {
                $note->{$note->getKeyName()} = (string) Str::uuid();
            }
        });
    }

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
