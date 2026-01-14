<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_movimentos_financeiros', function (Blueprint $table) {
            $table->id();

            // Controle multiempresa
            $table->string('empresa', 3)->index();

            // Identificadores Omie
            $table->bigInteger('codigo_movimento')->index();
            $table->bigInteger('codigo_lancamento_omie')->nullable()->index();
            $table->bigInteger('codigo_titulo')->nullable()->index();

            // Tipo / Origem
            $table->string('tipo_movimento', 30)->nullable(); // PAGAMENTO / RECEBIMENTO / TRANSFERENCIA
            $table->string('origem', 30)->nullable(); // Pagar / Receber / Conta Corrente

            // Datas
            $table->date('data_movimento')->nullable();
            $table->date('data_competencia')->nullable();

            // Valores
            $table->decimal('valor', 15, 2)->nullable();

            // Conta
            $table->bigInteger('codigo_conta_corrente')->nullable()->index();

            // Distribuições
            $table->json('categorias')->nullable();
            $table->json('departamentos')->nullable();

            // Payload livre
            $table->json('info')->nullable();

            $table->timestamps();

            $table->unique(
                ['empresa', 'codigo_movimento'],
                'unieq_empresa_codigo_movimento'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_movimentos_financeiros');
    }
};
