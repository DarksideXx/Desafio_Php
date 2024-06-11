<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'cpf_cnpj', 'email', 'password', 'type',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Definindo regras de validação
    public static $rules = [
        'cpf_cnpj' => 'unique:users',
        'email' => 'unique:users',
        'password' => 'required|string|min:6',
        'type' => 'required|in:1,2',
    ];

    /**
     * Define a relação entre User e Wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
