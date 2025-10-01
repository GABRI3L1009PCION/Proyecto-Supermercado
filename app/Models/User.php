<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','telefono','foto','estado','role',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts  = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /* ========= Relaciones ========= */

    public function detalle()
    {
        return $this->hasOne(ClienteDetalle::class);
    }

    // Perfil de vendedor (tabla vendors), clave foránea vendors.user_id
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    public function pedidosCliente()
    {
        return $this->hasMany(Pedido::class, 'user_id');
    }

    public function pedidosRepartidor()
    {
        return $this->hasMany(Pedido::class, 'repartidor_id');
    }

    public function pedidoItemsRepartidor()
    {
        return $this->hasMany(PedidoItem::class, 'repartidor_id');
    }

    /* ========= Scopes / helpers ========= */

    public function scopeActive($q){ return $q->where('estado', 'activo'); }
    public function scopeRole($q, string $role){ return $q->where('role', $role); }

    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isEmpleado(): bool   { return $this->role === 'empleado'; }
    public function isRepartidor(): bool { return $this->role === 'repartidor'; }
    public function isVendedor(): bool   { return $this->role === 'vendedor'; }
    public function isCliente(): bool    { return $this->role === 'cliente'; }
    public function isActive(): bool     { return $this->estado === 'activo'; }

    /** ID del vendor (vendors.id) del usuario autenticado */
    public function vendorId(): ?int
    {
        return optional($this->vendor)->id;
    }

    /** Accesor opcional: $user->vendor_id */
    public function getVendorIdAttribute(): ?int
    {
        return $this->vendorId();
    }

    /** ¿Este user (vendedor) es dueño del producto? */
    public function ownsProducto(Producto $p): bool
    {
        return (int) $p->vendor_id === (int) $this->vendorId();
    }

    /** ¿Este user (vendedor) es dueño del item? */
    public function ownsPedidoItem(PedidoItem $pi): bool
    {
        return (int) $pi->vendor_id === (int) $this->vendorId();
    }

    /** Scope de conveniencia para filtrar por vendor_id */
    public function scopeForVendor($q, ?int $vendorId)
    {
        return $q->whereHas('vendor', fn($qq) => $qq->where('id', $vendorId));
    }
}
