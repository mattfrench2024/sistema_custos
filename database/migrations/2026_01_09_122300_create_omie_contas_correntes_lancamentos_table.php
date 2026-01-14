<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_contas_correntes_lancamentos', function (Blueprint $table) {

            $table->id();

            // Empresa
            $table->string('empresa_codigo', 5);
            $table->string('empresa_nome', 255);

            // Identificadores Omie
            $table->unsignedBigInteger('nCodLanc')->nullable();
            $table->string('cCodIntLanc', 100)->nullable();

            // Conta corrente
            $table->unsignedBigInteger('omie_cc_id')->nullable();

            // Dados financeiros
            $table->date('data_lancamento')->nullable();
            $table->decimal('valor', 15, 2)->default(0);
            $table->string('tipo', 1)->nullable(); // D / C
            $table->string('descricao', 255)->nullable();

            // Relacionamentos financeiros
            $table->unsignedBigInteger('codigo_titulo')->nullable();
            $table->string('origem', 50)->nullable(); // pagar | receber | transferencia | ajuste

            // Controle
            $table->json('payload');
            $table->timestamp('importado_em')->useCurrent();

            $table->timestamps();

            // Índices
            $table->unique(['empresa_codigo', 'nCodLanc'], 'uniq_empresa_lanc');
            $table->index('omie_cc_id');
            $table->index('data_lancamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_contas_correntes_lancamentos');
    }
};
