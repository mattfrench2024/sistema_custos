<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('omie_clientes', function (Blueprint $table) {
            $table->string('empresa', 2)
                  ->after('id')
                  ->comment('04, 30 ou 36');
            
            $table->index('empresa');
        });
    }

    public function down(): void
    {
        Schema::table('omie_clientes', function (Blueprint $table) {
            $table->dropIndex(['empresa']);
            $table->dropColumn('empresa');
        });
    }
};
