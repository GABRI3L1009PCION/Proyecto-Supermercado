<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'facturacion')) {
                $table->json('facturacion')->nullable()->after('direccion_envio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'facturacion')) {
                $table->dropColumn('facturacion');
            }
        });
    }
};
