<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuarios';

    protected $primaryKey = 'Id';

    const CREATED_AT = 'dataCreated';
    const UPDATED_AT = 'dataUpdated';

    protected $fillable = [
        'nome',
        'idade',
        'email',
        'senha',
        'status',
        'is_admin',
    ];

    protected $hidden = [
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'idade'    => 'integer',
        ];
    }

    public function viagens(): HasMany
    {
        return $this->hasMany(Trip::class, 'usuario_id', 'Id');
    }
}
