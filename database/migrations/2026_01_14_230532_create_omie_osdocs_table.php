<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_osdocs', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 5);
            $table->string('tipo_doc', 20); // OS, NFSe, Recibo, ViaUnica
            $table->string('numero', 20)->nullable(); // cNumOs, cNumNFSe, cNumRecibo
            $table->string('serie', 10)->nullable(); // cSerieNFSe, cSerieViaUnica
            $table->string('url_pdf', 255)->nullable(); // cPdfOs, cPdfNFSe, cPdfRecibo
            $table->string('url_xml', 255)->nullable(); // cXmlNFSe
            $table->string('link_portal', 255)->nullable(); // cLinkPortal
            $table->string('cod_status', 10)->nullable();
            $table->string('des_status', 255)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_osdocs');
    }
};
