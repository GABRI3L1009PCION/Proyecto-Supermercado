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
        Schema::table('reseñas', function (Blueprint $table) {
            $table->text('respuesta_vendedor')->nullable()->after('comentario');
        });
    }

    public function down()
    {
        Schema::table('reseñas', function (Blueprint $table) {
            $table->dropColumn('respuesta_vendedor');
        });
    }


    /**
     * Reverse the migrations.
     */

};
