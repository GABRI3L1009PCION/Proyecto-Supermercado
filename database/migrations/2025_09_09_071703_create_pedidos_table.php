<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();

            // Información financiera
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('envio', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Información de pago
            $table->string('metodo_pago', 50)->nullable();
            $table->enum('estado_pago', ['pendiente','pagado','fallido'])->default('pendiente');

            // Dirección de envío (JSON con toda la información)
            $table->json('direccion_envio')->nullable();

            // Estado global del pedido
            $table->enum('estado_global', [
                'pendiente',
                'preparando',
                'listo',
                'entregado',
                'cancelado'
            ])->default('pendiente');

            // Código único del pedido
            $table->string('codigo', 20)->nullable()->unique();

            // Timestamps
            $table->timestamps();

            // Índices para mejor performance
            $table->index('estado_pago');
            $table->index('estado_global');
            $table->index('codigo');
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedidos');
    }
};
