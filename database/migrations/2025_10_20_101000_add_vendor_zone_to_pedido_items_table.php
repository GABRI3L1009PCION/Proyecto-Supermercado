<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_items', 'vendor_zone_id')) {
                $table->foreignId('vendor_zone_id')
                    ->nullable()
                    ->after('delivery_mode')
                    ->constrained('vendor_delivery_zones')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_items', 'vendor_zone_id')) {
                $table->dropConstrainedForeignId('vendor_zone_id');
            }
        });
    }
};
