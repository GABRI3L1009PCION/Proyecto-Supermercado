<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'provider',
        'external_id',
        'monto',
        'status',
        'payload',
    ];

    protected $casts = [
        'monto'   => 'decimal:2',
        'payload' => 'array',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
