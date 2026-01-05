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
    Schema::create('omie_pagar', function (Blueprint $table) {
        $table->id();
        $table->string('empresa', 2);
        $table->bigInteger('codigo_cliente_fornecedor')->nullable();
        $table->bigInteger('codigo_lancamento_omie')->nullable();
        $table->string('codigo_categoria')->nullable();
        $table->string('codigo_tipo_documento')->nullable();
        $table->string('status_titulo')->nullable();
        $table->date('data_emissao')->nullable();
        $table->date('data_vencimento')->nullable();
        $table->decimal('valor_documento', 15, 2)->nullable();
        $table->json('categorias')->nullable();
        $table->json('distribuicao')->nullable();
        $table->json('info')->nullable();
        $table->string('id_conta_corrente')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omie_pagar');
    }
};
