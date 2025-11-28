<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {

            // Se a coluna não existir, criamos
            if (!Schema::hasColumn('expenses', 'categoria_id')) {
                $table->unsignedBigInteger('categoria_id')->after('data');
            }

            // Garantir que não exista FK antiga com o mesmo nome
            $table->dropForeign(['categoria_id'])->ignore();
            
            // Criar FK correta
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'categoria_id')) {
                $table->dropForeign(['categoria_id']);
                $table->dropColumn('categoria_id');
            }
        });
    }
};
