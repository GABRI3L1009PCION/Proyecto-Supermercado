<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reseñas', function (Blueprint $table) {
            $table->id();

            // 🔹 Llaves foráneas
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');

            // 🔹 Datos de la reseña
            $table->unsignedTinyInteger('estrellas')->default(5); // 1–5
            $table->text('comentario')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseñas');
    }
};
