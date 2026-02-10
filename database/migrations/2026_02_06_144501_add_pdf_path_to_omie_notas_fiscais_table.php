<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/xxxx_add_pdf_path_to_omie_notas_fiscais_table.php

Schema::table('omie_notas_fiscais', function (Blueprint $table) {
    $table->string('pdf_path')->nullable()->after('possui_pdf');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('omie_notas_fiscais', function (Blueprint $table) {
            //
        });
    }
};
