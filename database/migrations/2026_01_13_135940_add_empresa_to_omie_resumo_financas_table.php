<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('omie_resumo_financas', function (Blueprint $table) {
            $table->string('empresa', 3)->after('id');
        });

        Schema::table('omie_resumo_financas', function (Blueprint $table) {
            $table->dropUnique(['data_referencia']);
            $table->unique(['empresa', 'data_referencia']);
        });
    }

    public function down()
    {
        Schema::table('omie_resumo_financas', function (Blueprint $table) {
            $table->dropUnique(['empresa', 'data_referencia']);
            $table->unique(['data_referencia']);
            $table->dropColumn('empresa');
        });
    }
};
