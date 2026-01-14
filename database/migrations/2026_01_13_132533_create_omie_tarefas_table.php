<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_tarefas', function (Blueprint $table) {
            $table->id();

            // Empresa (SV | VS | GV)
            $table->string('empresa', 3)->index();

            // Identificadores Omie
            $table->unsignedBigInteger('codigo_tarefa')->nullable()->index();
            $table->unsignedBigInteger('codigo_oportunidade')->nullable()->index();
            $table->string('codigo_integracao', 20)->nullable()->index();

            // Usuário / Atividade
            $table->unsignedBigInteger('codigo_usuario')->nullable()->index();
            $table->unsignedBigInteger('codigo_atividade')->nullable();

            // Data / Hora
            $table->date('data_tarefa')->nullable();
            $table->time('hora_tarefa')->nullable();

            // Flags
            $table->boolean('importante')->default(false);
            $table->boolean('urgente')->default(false);
            $table->boolean('em_execucao')->default(false);
            $table->boolean('realizada')->default(false);

            // Descrição
            $table->text('descricao')->nullable();

            // Detalhes oportunidade
            $table->json('detalhes_oportunidade')->nullable();

            // Payload bruto
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->unique(['empresa', 'codigo_tarefa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_tarefas');
    }
};
