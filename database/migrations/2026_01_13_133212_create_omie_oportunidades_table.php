<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_oportunidades', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('codigo_oportunidade')->unique();
            $table->string('titulo');
            $table->string('etapa')->nullable();
            $table->string('status')->nullable();

            $table->bigInteger('codigo_cliente')->nullable();
            $table->string('cliente')->nullable();

            $table->decimal('valor_previsto', 15, 2)->nullable();
            $table->date('data_prevista_fechamento')->nullable();

            $table->bigInteger('codigo_usuario_responsavel')->nullable();
            $table->string('usuario_responsavel')->nullable();

            $table->timestamp('data_criacao')->nullable();
            $table->timestamp('data_alteracao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_oportunidades');
    }
};
