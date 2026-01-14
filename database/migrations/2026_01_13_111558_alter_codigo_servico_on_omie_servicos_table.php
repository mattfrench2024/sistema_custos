<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('omie_servicos', function (Blueprint $table) {
            $table->unsignedBigInteger('codigo_servico')->change();
        });
    }

    public function down(): void
    {
        Schema::table('omie_servicos', function (Blueprint $table) {
            $table->unsignedInteger('codigo_servico')->change();
        });
    }
};

