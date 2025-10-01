<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();

            // Información del producto
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);

            // Estado de fulfillment
            $table->enum('fulfillment_status', [
                'accepted',
                'preparing',
                'ready',
                'delivered',
                'canceled'
            ])->default('accepted');

            // Justificación para cancelación (opcional)
            $table->text('justificacion')->nullable();

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('vendor_id');
            $table->index('fulfillment_status');
            $table->index(['pedido_id', 'vendor_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedido_items');
    }
};
