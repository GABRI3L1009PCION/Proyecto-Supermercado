<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente',
                'asignado',
                'aceptado',
                'en_camino',
                'entregado',
                'incidencia',
                'cancelado',
            ])->default('pendiente')->after('estado_global');

            $table->timestamp('fecha_asignado')->nullable()->after('estado');
            $table->timestamp('fecha_aceptado')->nullable()->after('fecha_asignado');
            $table->timestamp('fecha_salida')->nullable()->after('fecha_aceptado');
            $table->timestamp('fecha_entregado')->nullable()->after('fecha_salida');
            $table->timestamp('fecha_incidencia')->nullable()->after('fecha_entregado');

            $table->string('motivo_incidencia')->nullable()->after('fecha_incidencia');
            $table->string('evidencia_firma')->nullable()->after('motivo_incidencia');

            $table->boolean('urgente')->default(false)->after('evidencia_firma');
            $table->timestamp('hora_limite_entrega')->nullable()->after('urgente');

            $table->decimal('latitud_entrega', 10, 7)->nullable()->after('hora_limite_entrega');
            $table->decimal('longitud_entrega', 10, 7)->nullable()->after('latitud_entrega');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'estado',
                'fecha_asignado',
                'fecha_aceptado',
                'fecha_salida',
                'fecha_entregado',
                'fecha_incidencia',
                'motivo_incidencia',
                'evidencia_firma',
                'urgente',
                'hora_limite_entrega',
                'latitud_entrega',
                'longitud_entrega',
            ]);
        });
    }
};
