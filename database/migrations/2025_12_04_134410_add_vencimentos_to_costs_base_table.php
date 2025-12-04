<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('costs_base', function (Blueprint $table) {

        $months = [
            'jan' => '01',
            'fev' => '02',
            'mar' => '03',
            'abr' => '04',
            'mai' => '05',
            'jun' => '06',
            'jul' => '07',
            'ago' => '08',
            'set' => '09',
            'out' => '10',
            'nov' => '11',
            'dez' => '12',
        ];

        foreach ($months as $m => $num) {
            // exemplo default: 2024-02-05
            $default = date('Y') . '-' . $num . '-05';
            $table->date("venc_{$m}")->default($default)->nullable();
            $table->string("file_{$m}")->nullable();
        }

    });
}

public function down()
{
    Schema::table('costs_base', function (Blueprint $table) {

        foreach (['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'] as $m) {
            $table->dropColumn("venc_{$m}");
            $table->dropColumn("file_{$m}");
        }

    });
}
};
