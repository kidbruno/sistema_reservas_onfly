<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory;

    protected $table = 'viagens';

    protected $primaryKey = 'Id';

    public const CREATED_AT = 'dataCreated';
    public const UPDATED_AT = 'dataUpdated';

    public const STATUS_SOLICITADO = 'solicitada';
    public const STATUS_APROVADO = 'aprovada';
    public const STATUS_CANCELADO = 'cancelada';

    protected $fillable = [
        'usuario_id',
        'destino',
        'partida_de',
        'retorno_de',
        'data_viagem_ida',
        'data_viagem_volta',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_viagem_ida'   => 'date',
            'data_viagem_volta' => 'date',
            'usuario_id'        => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id', 'Id');
    }
}