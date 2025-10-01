<?php

namespace App\Policies;

use App\Models\PedidoItem;
use App\Models\User;

class PedidoItemPolicy
{
    public function view(User $user, PedidoItem $item): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'vendedor' && optional($user->vendor)->id === $item->vendor_id) return true;
        if ($user->role === 'repartidor' && $user->id === $item->repartidor_id) return true;
        if ($user->role === 'empleado' && is_null($item->vendor_id)) return true; // solo items del súper
        return false;
    }

    public function update(User $user, PedidoItem $item): bool
    {
        return $this->view($user, $item);
    }
}

