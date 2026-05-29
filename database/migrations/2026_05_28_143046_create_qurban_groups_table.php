<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qurban_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Sapi 1, Kambing 1
            $table->string('animal_type'); // sapi, kambing
            $table->decimal('purchase_price', 15, 2)->nullable(); // Harga beli asli (real cost)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qurban_groups');
    }
};
