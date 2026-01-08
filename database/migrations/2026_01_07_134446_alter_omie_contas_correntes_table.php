<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('omie_contas_correntes', function (Blueprint $table) {

            $table->string('tipo')->nullable()->after('descricao');
            $table->string('codigo_agencia', 20)->nullable()->after('codigo_banco');
            $table->string('numero_conta_corrente', 50)->nullable()->after('codigo_agencia');

            $table->string('inativo', 1)->default('N')->after('numero_conta_corrente');
            $table->string('importado_api', 1)->default('N')->after('inativo');

            $table->decimal('valor_limite', 15, 2)->default(0)->after('saldo_atual');

            $table->date('data_inc')->nullable()->after('valor_limite');
            $table->date('data_alt')->nullable()->after('data_inc');

            $table->unique(['empresa_codigo', 'omie_cc_id'], 'uniq_empresa_cc');
        });
    }

    public function down(): void
    {
        Schema::table('omie_contas_correntes', function (Blueprint $table) {

            $table->dropUnique('uniq_empresa_cc');

            $table->dropColumn([
                'tipo',
                'codigo_agencia',
                'numero_conta_corrente',
                'inativo',
                'importado_api',
                'valor_limite',
                'data_inc',
                'data_alt',
            ]);
        });
    }
};
