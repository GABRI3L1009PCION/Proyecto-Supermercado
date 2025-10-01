<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cart_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $t->unsignedInteger('cantidad');
            $t->decimal('precio_unitario_snapshot', 12, 2); // precio cuando se agregó
            $t->timestamps();

            $t->unique(['cart_id','producto_id']); // 1 fila por producto
        });
    }
    public function down(): void { Schema::dropIfExists('cart_items'); }
};
