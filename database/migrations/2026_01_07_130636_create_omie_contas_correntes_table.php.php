<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_contas_correntes', function (Blueprint $table) {
            $table->id();

            $table->string('empresa_codigo', 5);
            $table->string('empresa_nome');

            $table->unsignedBigInteger('omie_cc_id')->nullable(); // nCodCC
            $table->string('codigo_interno')->nullable(); // cCodCCInt

            $table->string('tipo_conta', 5)->nullable();
            $table->string('codigo_banco', 10)->nullable();
            $table->string('descricao')->nullable();

            $table->decimal('saldo_inicial', 15, 2)->default(0);
            $table->decimal('saldo_atual', 15, 2)->default(0);

            $table->timestamp('importado_em')->useCurrent();

            $table->timestamps();

            $table->unique(['empresa_codigo', 'omie_cc_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_contas_correntes');
    }
};
