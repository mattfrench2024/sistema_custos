<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_attachments', function (Blueprint $table) {
            $table->id();

            // vínculo com cost_base
            $table->unsignedBigInteger('cost_base_id');
            $table->foreign('cost_base_id')->references('id')->on('costs_base')->onDelete('cascade');

            // mês referente da nota
            $table->string('mes'); // 'jan','fev', etc

            // valor pago (pode ser diferente ou igual ao da tabela)
            $table->decimal('valor', 15, 2)->nullable();

            // caminho do arquivo
            $table->string('arquivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_attachments');
    }
};
