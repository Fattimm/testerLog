<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * Les attributs pouvant être remplis par l'utilisateur.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Les attributs cachés pour les tableaux JSON.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
