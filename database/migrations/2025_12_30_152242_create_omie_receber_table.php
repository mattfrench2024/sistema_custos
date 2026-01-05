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
       Schema::create('omie_receber', function (Blueprint $table) {
    $table->id();

    $table->string('empresa')->index();

    $table->string('codigo_lancamento_integracao')->unique();
    $table->unsignedBigInteger('codigo_cliente_fornecedor');
    $table->date('data_vencimento');
    $table->date('data_previsao')->nullable();
    $table->decimal('valor_documento', 15, 2);
    $table->string('codigo_categoria');
    $table->unsignedBigInteger('id_conta_corrente');

    $table->string('status')->default('pendente'); // pendente | exportado | erro
    $table->json('payload')->nullable();
    $table->json('retorno_omie')->nullable();
    $table->text('erro')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omie_receber');
    }
};
