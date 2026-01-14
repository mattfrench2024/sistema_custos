<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omie_orcamentos', function (Blueprint $table) {
            $table->id();

            $table->string('empresa')->index();
            $table->integer('ano')->nullable();
            $table->integer('mes')->nullable();

            $table->string('codigo_categoria', 20)->nullable();
            $table->string('descricao_categoria')->nullable();

            $table->decimal('valor_previsto', 15, 2)->default(0);
            $table->decimal('valor_realizado', 15, 2)->default(0);

            $table->json('payload')->nullable();

            $table->timestamps();

            $table->unique(['empresa', 'ano', 'mes', 'codigo_categoria'], 'uc_empresa_ano_mes_categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_orcamentos');
    }
};
