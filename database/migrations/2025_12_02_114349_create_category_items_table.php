<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('category_items', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();       // Nome do item (ex: "HeadSet")
            $table->string('tipo')->default('software'); // Tipo futuro (software, serviço, etc)
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('category_items');
    }
};
