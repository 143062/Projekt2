<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    // Wypełnialne kolumny
    protected $fillable = ['login', 'email', 'password', 'profile_picture', 'role_id'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Timestampy
    public $timestamps = true;

    /**
     * Automatyczne generowanie UUID.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relacje: użytkownik należy do roli
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Relacja: użytkownik ma wiele notatek
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    // Relacja: użytkownik jest znajomym wielu użytkowników
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
    }

    // Relacja: użytkownik ma udostępnione notatki
    public function sharedNotes()
    {
        return $this->hasMany(SharedNote::class, 'user_id');
    }
}
