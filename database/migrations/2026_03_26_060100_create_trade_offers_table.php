<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('want_type');
            $table->string('want_label');
            $table->string('want_details')->nullable();
            $table->string('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_offers');
    }
};
