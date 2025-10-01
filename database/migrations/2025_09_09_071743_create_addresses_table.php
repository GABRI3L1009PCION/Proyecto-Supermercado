<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->enum('tipo', ['envio','facturacion'])->default('envio');
            $t->string('linea1');
            $t->string('linea2')->nullable();
            $t->string('zona')->nullable();
            $t->string('municipio')->default('Puerto Barrios');
            $t->string('departamento')->default('Izabal');
            $t->decimal('geo_lat', 10, 7)->nullable();
            $t->decimal('geo_lng', 10, 7)->nullable();
            $t->json('referencias')->nullable();
            $t->boolean('is_default')->default(false);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('addresses'); }
};
