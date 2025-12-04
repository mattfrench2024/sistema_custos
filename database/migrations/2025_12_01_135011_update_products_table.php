<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Nova unidade de cobrança
            $table->string('unidade')->nullable()->after('valor');

            // Texto opcional
            $table->string('observacao')->nullable()->after('unidade');

            // Modificar coluna descrição para string 255
            $table->string('descricao', 255)->nullable()->change();

            // Flags de sistema e ativo
            $table->boolean('is_active')->default(true)->after('descricao');
            $table->boolean('is_system')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unidade', 'observacao', 'is_active', 'is_system']);
            $table->text('descricao')->nullable()->change();
        });
    }
};
