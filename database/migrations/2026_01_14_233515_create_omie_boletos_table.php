<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('omie_boletos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 10);
            $table->string('cCodIntTitulo', 60)->unique();
            $table->integer('nCodTitulo')->nullable();
            $table->string('cNumBoleto', 30)->nullable();
            $table->string('cCodBarras', 70)->nullable();
            $table->decimal('nPerJuros', 8, 2)->nullable();
            $table->decimal('nPerMulta', 8, 2)->nullable();
            $table->string('cNumBancario', 30)->nullable();
            $table->string('dDtEmBol', 10)->nullable();
            $table->string('dDtVenc', 10)->nullable();
            $table->string('cLinkBoleto', 500)->nullable();
            $table->string('cCodStatus', 4)->nullable();
            $table->text('cDesStatus')->nullable();
            $table->decimal('vDescontoCond1', 10, 2)->nullable();
            $table->string('dDescontoCond1', 10)->nullable();
            $table->decimal('vDescontoCond2', 10, 2)->nullable();
            $table->string('dDescontoCond2', 10)->nullable();
            $table->decimal('vDescontoCond3', 10, 2)->nullable();
            $table->string('dDescontoCond3', 10)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omie_boletos');
    }
};
