<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_categorias', function (Blueprint $table) {
            $table->id();

            $table->string('empresa', 2)->index(); // 04 | 30 | 36

            $table->string('codigo', 20);
            $table->string('descricao', 100);
            $table->string('descricao_padrao', 100)->nullable();

            $table->string('categoria_superior', 20)->nullable();
            $table->string('codigo_dre', 20)->nullable();

            $table->boolean('conta_receita')->default(false);
            $table->boolean('conta_despesa')->default(false);
            $table->boolean('totalizadora')->default(false);
            $table->boolean('transferencia')->default(false);
            $table->boolean('conta_inativa')->default(false);
            $table->boolean('nao_exibir')->default(false);
            $table->boolean('definida_pelo_usuario')->default(false);

            $table->string('natureza', 255)->nullable();

            // Dados DRE
            $table->string('dre_codigo', 20)->nullable();
            $table->string('dre_descricao', 100)->nullable();
            $table->integer('dre_nivel')->nullable();
            $table->string('dre_sinal', 1)->nullable();
            $table->boolean('dre_totaliza')->default(false);
            $table->boolean('dre_nao_exibir')->default(false);

            $table->json('payload');

            $table->timestamps();

            $table->unique(['empresa', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_categorias');
    }
};
