<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omie_documentos_fiscais', function (Blueprint $table) {
            $table->id();

            $table->string('empresa', 2)->index();

            $table->string('modelo', 2)->nullable(); // 55, 65, 57, etc
            $table->string('numero', 10)->nullable();
            $table->string('serie', 10)->nullable();
            $table->string('chave', 44)->nullable()->unique();

            $table->date('data_emissao')->nullable();
            $table->time('hora_emissao')->nullable();

            $table->decimal('valor', 15, 2)->nullable();
            $table->string('status', 1)->nullable();

            $table->unsignedBigInteger('omie_id_nf')->nullable();
            $table->unsignedBigInteger('omie_id_pedido')->nullable();
            $table->unsignedBigInteger('omie_id_os')->nullable();
            $table->unsignedBigInteger('omie_id_ct')->nullable();
            $table->unsignedBigInteger('omie_id_receb')->nullable();
            $table->unsignedBigInteger('omie_id_cupom')->nullable();

            $table->longText('xml')->nullable();

            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['empresa', 'modelo']);
            $table->index(['data_emissao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_documentos_fiscais');
    }
};
