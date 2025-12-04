<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cost_base_id');
            $table->decimal('value', 10, 2);
            $table->string('description')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();

            $table->foreign('cost_base_id')
                  ->references('id')
                  ->on('costs_base')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_entries');
    }
};
