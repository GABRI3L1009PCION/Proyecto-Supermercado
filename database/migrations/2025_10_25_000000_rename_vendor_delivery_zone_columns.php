<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_delivery_zones')) {
            return;
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'vendor_id') && !Schema::hasColumn('vendor_delivery_zones', 'seller_id')) {
            Schema::table('vendor_delivery_zones', function (Blueprint $table) {
                $table->dropForeign(['vendor_id']);
            });

            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN vendor_id TO seller_id');

            Schema::table('vendor_delivery_zones', function (Blueprint $table) {
                $table->foreign('seller_id')->references('id')->on('vendors')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'coverage') && !Schema::hasColumn('vendor_delivery_zones', 'descripcion_cobertura')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN coverage TO descripcion_cobertura');
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'delivery_fee') && !Schema::hasColumn('vendor_delivery_zones', 'tarifa_reparto')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN delivery_fee TO tarifa_reparto');
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'activo') && !Schema::hasColumn('vendor_delivery_zones', 'activa')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN activo TO activa');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vendor_delivery_zones')) {
            return;
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'seller_id') && !Schema::hasColumn('vendor_delivery_zones', 'vendor_id')) {
            Schema::table('vendor_delivery_zones', function (Blueprint $table) {
                $table->dropForeign(['seller_id']);
            });

            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN seller_id TO vendor_id');

            Schema::table('vendor_delivery_zones', function (Blueprint $table) {
                $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'descripcion_cobertura') && !Schema::hasColumn('vendor_delivery_zones', 'coverage')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN descripcion_cobertura TO coverage');
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'tarifa_reparto') && !Schema::hasColumn('vendor_delivery_zones', 'delivery_fee')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN tarifa_reparto TO delivery_fee');
        }

        if (Schema::hasColumn('vendor_delivery_zones', 'activa') && !Schema::hasColumn('vendor_delivery_zones', 'activo')) {
            DB::statement('ALTER TABLE vendor_delivery_zones RENAME COLUMN activa TO activo');
        }
    }
};
