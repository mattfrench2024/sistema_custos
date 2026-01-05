<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->tinyInteger('status_jan')->default(1);
            $table->tinyInteger('status_fev')->default(1);
            $table->tinyInteger('status_mar')->default(1);
            $table->tinyInteger('status_abr')->default(1);
            $table->tinyInteger('status_mai')->default(1);
            $table->tinyInteger('status_jun')->default(1);
            $table->tinyInteger('status_jul')->default(1);
            $table->tinyInteger('status_ago')->default(1);
            $table->tinyInteger('status_set')->default(1);
            $table->tinyInteger('status_out')->default(1);
            $table->tinyInteger('status_nov')->default(1);
            $table->tinyInteger('status_dez')->default(1);
        });

        // 🔥 Deixa TODO mundo no banco como pago
        DB::table('costs_base')->update([
            'status_jan' => 1,
            'status_fev' => 1,
            'status_mar' => 1,
            'status_abr' => 1,
            'status_mai' => 1,
            'status_jun' => 1,
            'status_jul' => 1,
            'status_ago' => 1,
            'status_set' => 1,
            'status_out' => 1,
            'status_nov' => 1,
            'status_dez' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('costs_base', function (Blueprint $table) {
            $table->dropColumn([
                'status_jan',
                'status_fev',
                'status_mar',
                'status_abr',
                'status_mai',
                'status_jun',
                'status_jul',
                'status_ago',
                'status_set',
                'status_out',
                'status_nov',
                'status_dez',
            ]);
        });
    }
};
