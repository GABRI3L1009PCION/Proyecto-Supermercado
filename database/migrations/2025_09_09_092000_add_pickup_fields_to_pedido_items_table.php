<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_items', 'pickup_contact')) {
                $table->string('pickup_contact', 120)->nullable()->after('justificacion');
            }
            if (!Schema::hasColumn('pedido_items', 'pickup_phone')) {
                $table->string('pickup_phone', 45)->nullable()->after('pickup_contact');
            }
            if (!Schema::hasColumn('pedido_items', 'pickup_address')) {
                $table->string('pickup_address', 255)->nullable()->after('pickup_phone');
            }
            if (!Schema::hasColumn('pedido_items', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('pickup_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_items', 'delivery_instructions')) {
                $table->dropColumn('delivery_instructions');
            }
            if (Schema::hasColumn('pedido_items', 'pickup_address')) {
                $table->dropColumn('pickup_address');
            }
            if (Schema::hasColumn('pedido_items', 'pickup_phone')) {
                $table->dropColumn('pickup_phone');
            }
            if (Schema::hasColumn('pedido_items', 'pickup_contact')) {
                $table->dropColumn('pickup_contact');
            }
        });
    }
};
