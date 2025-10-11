<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->string('titulo', 150)->nullable()->after('reaccion');
            $table->string('categoria_contexto', 40)->nullable()->after('titulo');
            $table->json('aspectos')->nullable()->after('categoria_contexto');
            $table->string('tiempo_uso', 40)->nullable()->after('aspectos');
        });
    }

    public function down(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'categoria_contexto', 'aspectos', 'tiempo_uso']);
        });
    }
};
