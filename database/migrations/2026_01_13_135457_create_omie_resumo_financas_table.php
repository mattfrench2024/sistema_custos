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
        Schema::create('omie_resumo_financas', function (Blueprint $table) {
    $table->id();

    // Data de referência do resumo
    $table->date('data_referencia')->index();

    // Conta corrente
    $table->decimal('saldo_contas', 15, 2)->nullable();
    $table->decimal('limite_credito', 15, 2)->nullable();

    // Contas a pagar
    $table->integer('qtd_pagar')->nullable();
    $table->decimal('total_pagar', 15, 2)->nullable();
    $table->decimal('total_pagar_atraso', 15, 2)->nullable();

    // Contas a receber
    $table->integer('qtd_receber')->nullable();
    $table->decimal('total_receber', 15, 2)->nullable();
    $table->decimal('total_receber_atraso', 15, 2)->nullable();

    // Fluxo de caixa
    $table->decimal('fluxo_pagar', 15, 2)->nullable();
    $table->decimal('fluxo_receber', 15, 2)->nullable();
    $table->decimal('fluxo_saldo', 15, 2)->nullable();

    // Identificação visual (API Omie)
    $table->string('icone', 100)->nullable();
    $table->string('cor', 20)->nullable();

    $table->timestamps();

    // Evita duplicidade por dia
    $table->unique(['data_referencia']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omie_resumo_financas');
    }
};
