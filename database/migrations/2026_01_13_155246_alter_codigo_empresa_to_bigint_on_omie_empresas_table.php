<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('omie_empresas', function (Blueprint $table) {
            $table->bigInteger('codigo_empresa')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('omie_empresas', function (Blueprint $table) {
            $table->integer('codigo_empresa')->nullable()->change();
        });
    }
};
