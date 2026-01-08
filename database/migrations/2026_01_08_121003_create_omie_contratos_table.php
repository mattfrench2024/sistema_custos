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
        Schema::create('omie_contratos', function (Blueprint $table) {
    $table->id();
    $table->string('empresa', 2);
    $table->bigInteger('nCodCtr')->unique(); // ID do contrato na Omie
    $table->string('cNumCtr')->nullable();   // Número do contrato (ex: 2024/001)
    $table->bigInteger('nCodCli');           // FK para omie_clientes
    $table->string('cCodCateg')->nullable();
    $table->decimal('nValTotMes', 15, 2);    // Valor recorrente mensal
    $table->date('dVigInicial')->nullable();
    $table->date('dVigFinal')->nullable();
    $table->string('cCodSit', 2);            // Situação (Ativo, Suspenso, etc)
    $table->json('itens')->nullable();       // Detalhes dos serviços contratados
    $table->json('payload')->nullable();     // Resposta completa para auditoria
    $table->timestamps();
    
    $table->index(['empresa', 'nCodCli']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omie_contratos');
    }
};
