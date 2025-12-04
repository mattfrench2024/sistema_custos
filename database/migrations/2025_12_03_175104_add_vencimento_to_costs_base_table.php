<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->date('vencimento')->nullable()->after('Categoria');
        });

        // Preenche vencimento existente com o dia 1 do mês atual
        DB::table('costs_base')->update([
            'vencimento' => Carbon::now()->startOfMonth()->toDateString()
        ]);
    }

    public function down()
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->dropColumn('vencimento');
        });
    }
};

