<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCostsBaseTable extends Migration
{
    public function up()
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->boolean('status')
                ->default(false)
                ->after('Ano');
        });
    }

    public function down()
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
