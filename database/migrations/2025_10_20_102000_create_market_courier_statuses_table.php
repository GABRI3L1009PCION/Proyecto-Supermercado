<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_courier_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status', 40)->default('disponible_para_reparto');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        DB::table('market_courier_statuses')->insert([
            'status'     => 'disponible_para_reparto',
            'note'       => 'Disponible para reparto',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('market_courier_statuses');
    }
};
