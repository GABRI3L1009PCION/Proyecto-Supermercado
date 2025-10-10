<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reseña_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseña_id')->constrained('reseñas')->onDelete('cascade');
            $table->string('ruta'); // ejemplo: uploads/reseñas/imagen1.jpg
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseña_imagenes');
    }

};
