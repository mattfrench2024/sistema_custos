<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('omie_dfe_docs', function (Blueprint $table) {
    $table->foreignId('nota_fiscal_id')
        ->nullable()
        ->after('id')
        ->constrained('omie_notas_fiscais')
        ->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('omie_dfe_docs', function (Blueprint $table) {
            //
        });
    }
};
