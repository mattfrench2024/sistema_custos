<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_empresas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 10)->index(); // gv, sv, vs
            $table->integer('codigo_empresa')->nullable()->index();
            $table->string('codigo_empresa_integracao', 20)->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->string('razao_social', 60)->nullable();
            $table->string('nome_fantasia', 60)->nullable();
            $table->string('logradouro', 50)->nullable();
            $table->string('endereco_numero', 10)->nullable();
            $table->string('complemento', 60)->nullable();
            $table->string('bairro', 60)->nullable();
            $table->string('cidade', 40)->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('codigo_pais', 4)->nullable();
            $table->string('telefone1', 20)->nullable();
            $table->string('telefone2', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('regime_tributario', 1)->nullable();
            $table->string('optante_simples_nacional', 1)->nullable();
            $table->string('gera_nfe', 1)->nullable();
            $table->string('gera_nfse', 1)->nullable();
            $table->string('inativa', 1)->nullable();
            $table->json('payload')->nullable(); // armazena o retorno completo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_empresas');
    }
};
