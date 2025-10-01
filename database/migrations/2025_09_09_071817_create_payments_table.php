<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $t->string('provider'); // 'efectivo','card','paypal','stripe', etc.
            $t->string('external_id')->nullable(); // id del proveedor
            $t->decimal('monto', 12, 2);
            $t->enum('status', ['pending','paid','failed','refunded'])->default('pending')->index();
            $t->json('payload')->nullable(); // respuesta cruda del gateway
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
