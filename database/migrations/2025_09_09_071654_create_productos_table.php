<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('categoria_id')->constrained('categorias');
            $t->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete()->index(); // null = producto del súper
            $t->string('nombre');
            $t->string('slug')->unique();
            $t->text('descripcion')->nullable();
            $t->decimal('precio', 12, 2);
            $t->unsignedInteger('stock')->default(0);
            $t->string('imagen')->nullable();
            $t->enum('estado', ['activo','inactivo'])->default('activo')->index();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
