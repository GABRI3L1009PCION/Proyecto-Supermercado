<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('delivery_zones')) {
            Schema::create('delivery_zones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150);
                $table->string('municipio', 120);
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->decimal('tarifa_base', 8, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            $now = now();
            $defaults = [
                ['nombre' => 'Zona Centro', 'municipio' => 'Puerto Barrios', 'lat' => 15.7275, 'lng' => -88.5954, 'tarifa_base' => 12.00],
                ['nombre' => 'La Coroza', 'municipio' => 'Puerto Barrios', 'lat' => 15.7160, 'lng' => -88.5790, 'tarifa_base' => 13.50],
                ['nombre' => 'Santo Tomás Centro', 'municipio' => 'Santo Tomás de Castilla', 'lat' => 15.7169, 'lng' => -88.5940, 'tarifa_base' => 11.50],
                ['nombre' => 'Barrio Las Flores', 'municipio' => 'Santo Tomás de Castilla', 'lat' => 15.7124, 'lng' => -88.6008, 'tarifa_base' => 13.00],
            ];

            foreach ($defaults as &$zone) {
                $zone['activo'] = true;
                $zone['created_at'] = $now;
                $zone['updated_at'] = $now;
            }

            DB::table('delivery_zones')->insert($defaults);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
