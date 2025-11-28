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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('codigo')->unique();
        $table->unsignedBigInteger('categoria_id');
        $table->decimal('valor', 10, 2);
        $table->text('descricao')->nullable();

        $table->timestamps();

        $table->foreign('categoria_id')
              ->references('id')->on('categories')
              ->onDelete('cascade');
    });
}

};
