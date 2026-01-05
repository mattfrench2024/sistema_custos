<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->renameColumn('Pago jan', 'pago_jan');
            $table->renameColumn('Pago fev', 'pago_fev');
            $table->renameColumn('Pago mar', 'pago_mar');
            $table->renameColumn('Pago abr', 'pago_abr');
            $table->renameColumn('Pago mai', 'pago_mai');
            $table->renameColumn('Pago jun', 'pago_jun');
            $table->renameColumn('Pago jul', 'pago_jul');
            $table->renameColumn('Pago ago', 'pago_ago');
            $table->renameColumn('Pago set', 'pago_set');
            $table->renameColumn('Pago out', 'pago_out');
            $table->renameColumn('Pago nov', 'pago_nov');
            $table->renameColumn('Pago dez', 'pago_dez');
        });
    }

    public function down(): void
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->renameColumn('pago_jan', 'Pago jan');
            $table->renameColumn('pago_fev', 'Pago fev');
            $table->renameColumn('pago_mar', 'Pago mar');
            $table->renameColumn('pago_abr', 'Pago abr');
            $table->renameColumn('pago_mai', 'Pago mai');
            $table->renameColumn('pago_jun', 'Pago jun');
            $table->renameColumn('pago_jul', 'Pago jul');
            $table->renameColumn('pago_ago', 'Pago ago');
            $table->renameColumn('pago_set', 'Pago set');
            $table->renameColumn('pago_out', 'Pago out');
            $table->renameColumn('pago_nov', 'Pago nov');
            $table->renameColumn('pago_dez', 'Pago dez');
        });
    }
};
