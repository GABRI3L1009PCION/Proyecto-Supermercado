<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->foreignId('pedido_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('pedidos')
                ->onDelete('cascade');

            $table->foreignId('pedido_item_id')
                ->nullable()
                ->after('pedido_id')
                ->constrained('pedido_items')
                ->onDelete('cascade');

            $table->unique(['pedido_item_id'], 'reseñas_pedido_item_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->dropUnique('reseñas_pedido_item_unique');
            $table->dropConstrainedForeignId('pedido_item_id');
            $table->dropConstrainedForeignId('pedido_id');
        });
    }
};
