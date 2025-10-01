<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vendors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['active','suspended'])->default('active')->index();
            $t->enum('pricing_mode', ['markup','commission'])->default('markup');
            $t->decimal('commission_rate', 5, 2)->default(0);
            $t->string('service_area')->nullable();
            $t->string('payout_bank_info')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vendors'); }
};
