<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_servicos', function (Blueprint $table) {
            $table->id();

            $table->string('empresa', 5);

            // Identificação Omie
            $table->integer('codigo_servico')->nullable();              // nCodServ
            $table->string('codigo_integracao')->nullable();            // cCodIntServ
            $table->string('codigo')->nullable();                       // cCodigo

            // Cabeçalho
            $table->string('descricao')->nullable();                    // cDescricao
            $table->decimal('preco_unitario', 15, 2)->nullable();       // nPrecoUnit
            $table->string('codigo_categoria')->nullable();             // cCodCateg

            // Status
            $table->string('importado_api', 1)->nullable();             // cImpAPI
            $table->string('inativo', 1)->nullable();                   // inativo

            // Estruturas complexas
            $table->json('cabecalho')->nullable();
            $table->json('descricao_completa')->nullable();
            $table->json('impostos')->nullable();
            $table->json('info')->nullable();
            $table->json('produtos_utilizados')->nullable();

            // Payload completo
            $table->json('payload');

            $table->timestamps();

            $table->unique(
                ['empresa', 'codigo_servico'],
                'unieq_empresa_codigo_servico'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_servicos');
    }
};
