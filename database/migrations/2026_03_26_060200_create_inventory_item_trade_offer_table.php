<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_trade_offer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_trade_offer');
    }
};
