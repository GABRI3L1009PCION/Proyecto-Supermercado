<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_items', 'delivery_mode')) {
                $table->string('delivery_mode')->default('pending')->after('fulfillment_status');
            }
            if (!Schema::hasColumn('pedido_items', 'delivery_fee')) {
                $table->decimal('delivery_fee', 8, 2)->default(0)->after('delivery_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_items', 'delivery_fee')) {
                $table->dropColumn('delivery_fee');
            }
            if (Schema::hasColumn('pedido_items', 'delivery_mode')) {
                $table->dropColumn('delivery_mode');
            }
        });
    }
};
