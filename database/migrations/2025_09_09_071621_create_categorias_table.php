<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias', function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('slug')->unique();
            $t->text('descripcion')->nullable();
            $t->string('estado')->default('activo'); // ← campo agregado con valor por defecto
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('categorias');
    }
};
