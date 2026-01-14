<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_produtos', function (Blueprint $table) {
            $table->id();

            $table->string('empresa', 5);

            // Identificação Omie
            $table->bigInteger('codigo_produto')->nullable();
            $table->string('codigo_produto_integracao')->nullable();
            $table->string('codigo')->nullable();

            // Dados principais
            $table->string('descricao')->nullable();
            $table->string('unidade', 10)->nullable();
            $table->string('ncm', 20)->nullable();

            // Flags importantes
            $table->string('tipo')->nullable(); // Produto / Serviço
            $table->string('importado_api', 1)->nullable();

            // Estruturas complexas
            $table->json('caracteristicas')->nullable();
            $table->json('componentes_kit')->nullable();
            $table->json('imagens')->nullable();
            $table->json('dados_ibpt')->nullable();
            $table->json('info')->nullable();

            // Payload completo Omie
            $table->json('payload');

            $table->timestamps();

            $table->unique(
                ['empresa', 'codigo_produto'],
                'unieq_empresa_codigo_produto'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_produtos');
    }
};
