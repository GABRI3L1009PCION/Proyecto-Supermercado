<?php
// app/Models/Vendor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    protected $fillable = [
        'user_id','status','pricing_mode','commission_rate','service_area','payout_bank_info'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function productos(): HasMany { return $this->hasMany(Producto::class); }
    public function items(): HasMany {
        return $this->hasMany(PedidoItem::class);
    }

    // Atajo útil
    public function getIsActiveAttribute(): bool { return $this->status === 'active'; }
}
