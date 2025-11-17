<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'delivery_price')) {
            return;
        }

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('delivery_price');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('productos', 'delivery_price')) {
            return;
        }

        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('delivery_price', 8, 2)->nullable()->after('precio');
        });
    }
};
