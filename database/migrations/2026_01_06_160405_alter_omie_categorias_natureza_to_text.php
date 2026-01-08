<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('omie_categorias', function (Blueprint $table) {
            $table->text('natureza')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('omie_categorias', function (Blueprint $table) {
            $table->string('natureza', 255)->nullable()->change();
        });
    }
};
