<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    // Wypełnialne kolumny
    protected $fillable = ['login', 'email', 'password', 'profile_picture', 'role_id'];

    // Klucz główny to UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // timestampy
    public $timestamps = true;

    // Automatyczne hashowanie hasła
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Relacja: użytkownik należy do roli
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
