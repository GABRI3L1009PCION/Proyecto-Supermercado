<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('guest_token', 64)->nullable()->index(); // carrito de invitado
            $t->string('currency', 3)->default('GTQ');
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('carts'); }
};
