<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_dfe_docs', function (Blueprint $table) {
    $table->id();

    $table->string('empresa', 5);

    $table->string('tipo_documento', 20); // NFE | CTE | CUPOM
    $table->unsignedBigInteger('id_documento_omie')->unique();

    $table->string('numero')->nullable();
    $table->string('chave_acesso', 50)->nullable();

    $table->date('data_emissao')->nullable();

    $table->longText('xml')->nullable();
    $table->string('pdf_url')->nullable();
    $table->string('portal_url')->nullable();

    $table->string('status_codigo', 10)->nullable();
    $table->text('status_descricao')->nullable();

    $table->json('payload')->nullable();

    $table->timestamps();

    $table->index(['empresa', 'tipo_documento']);
});

    }

    public function down(): void
{
    Schema::dropIfExists('omie_dfe_docs');
}

};
