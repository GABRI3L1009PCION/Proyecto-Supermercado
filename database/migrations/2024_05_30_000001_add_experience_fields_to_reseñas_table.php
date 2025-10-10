<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->unsignedTinyInteger('uso_score')->nullable()->after('estrellas');
            $table->unsignedTinyInteger('comodidad_score')->nullable()->after('uso_score');
            $table->unsignedTinyInteger('duracion_score')->nullable()->after('comodidad_score');
            $table->string('talla_percibida', 20)->nullable()->after('duracion_score');
            $table->string('reaccion', 50)->nullable()->after('talla_percibida');
        });
    }

    public function down(): void
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->dropColumn([
                'uso_score',
                'comodidad_score',
                'duracion_score',
                'talla_percibida',
                'reaccion',
            ]);
        });
    }
};
