<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_notas_fiscais', function (Blueprint $table) {
            $table->id();

            $table->string('empresa', 5)->index();

            // Tipo fiscal real
            $table->enum('tipo', ['NFE', 'NFSE', 'CTE', 'CUPOM']);

            // IDs Omie
            $table->unsignedBigInteger('id_nota_omie')->index();
            $table->string('numero', 50)->nullable();
            $table->string('serie', 20)->nullable();

            // Chave fiscal
            $table->string('chave_acesso', 50)->nullable()->index();

            // Datas
            $table->date('data_emissao')->nullable();
            $table->dateTime('data_autorizacao')->nullable();

            // Valores
            $table->decimal('valor_total', 15, 2)->nullable();

            // Status
            $table->string('status', 30)->nullable();
            $table->string('codigo_status', 10)->nullable();

            // Pessoas
            $table->string('cnpj_emitente', 20)->nullable();
            $table->string('cnpj_destinatario', 20)->nullable();

            // Controle
            $table->boolean('possui_xml')->default(false);
            $table->boolean('possui_pdf')->default(false);

            // Payload bruto
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->unique(['empresa', 'tipo', 'id_nota_omie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_notas_fiscais');
    }
};
