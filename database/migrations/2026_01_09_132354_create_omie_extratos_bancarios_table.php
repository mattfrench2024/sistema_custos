<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omie_extratos_bancarios', function (Blueprint $table) {
            $table->id();

            $table->string('empresa_codigo', 5)->index();
            $table->string('empresa_nome', 255);

            $table->unsignedBigInteger('omie_cc_id')->index();
            $table->string('codigo_interno_cc')->nullable();

            $table->date('data_movimento')->nullable();
            $table->date('data_compensacao')->nullable();

            $table->decimal('valor', 15, 2)->default(0);
            $table->string('tipo', 1)->nullable(); // C ou D

            $table->string('descricao', 255)->nullable();
            $table->string('documento', 100)->nullable();

            $table->decimal('saldo_pos', 15, 2)->nullable();

            $table->json('payload');

            $table->timestamp('importado_em')->useCurrent();

            $table->timestamps();

            $table->unique(
                ['empresa_codigo', 'omie_cc_id', 'data_movimento', 'valor', 'descricao'],
                'uniext_bancario'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_extratos_bancarios');
    }
};
