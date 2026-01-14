<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmieMfTable extends Migration
{
    public function up()
    {
        Schema::create('omie_mf', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 5);
            $table->integer('nCodTitulo')->nullable();
            $table->string('cCodIntTitulo', 60)->nullable()->index();
            $table->string('cNumTitulo', 20)->nullable();
            $table->date('dDtEmissao')->nullable();
            $table->date('dDtVenc')->nullable();
            $table->date('dDtPrevisao')->nullable();
            $table->date('dDtPagamento')->nullable();
            $table->integer('nCodCliente')->nullable();
            $table->string('cCPFCNPJCliente', 20)->nullable();
            $table->integer('nCodCtr')->nullable();
            $table->string('cNumCtr', 20)->nullable();
            $table->integer('nCodOS')->nullable();
            $table->string('cNumOS', 15)->nullable();
            $table->integer('nCodCC')->nullable();
            $table->string('cStatus', 100)->nullable();
            $table->string('cNatureza', 1)->nullable();
            $table->string('cTipo', 5)->nullable();
            $table->string('cOperacao', 2)->nullable();
            $table->string('cNumDocFiscal', 20)->nullable();
            $table->string('cCodCateg', 20)->nullable();
            $table->string('cNumParcela', 7)->nullable();
            $table->decimal('nValorTitulo', 15, 2)->nullable();
            $table->decimal('nValorPIS', 15, 2)->nullable();
            $table->string('cRetPIS', 1)->nullable();
            $table->decimal('nValorCOFINS', 15, 2)->nullable();
            $table->string('cRetCOFINS', 1)->nullable();
            $table->decimal('nValorCSLL', 15, 2)->nullable();
            $table->string('cRetCSLL', 1)->nullable();
            $table->decimal('nValorIR', 15, 2)->nullable();
            $table->string('cRetIR', 1)->nullable();
            $table->decimal('nValorISS', 15, 2)->nullable();
            $table->string('cRetISS', 1)->nullable();
            $table->decimal('nValorINSS', 15, 2)->nullable();
            $table->string('cRetINSS', 1)->nullable();
            $table->integer('cCodProjeto')->nullable();
            $table->text('observacao')->nullable();
            $table->integer('cCodVendedor')->nullable();
            $table->integer('nCodComprador')->nullable();
            $table->string('cCodigoBarras', 70)->nullable();
            $table->string('cNSU', 100)->nullable();
            $table->integer('nCodNF')->nullable();
            $table->date('dDtRegistro')->nullable();
            $table->string('cNumBoleto', 30)->nullable();
            $table->string('cChaveNFe', 44)->nullable();
            $table->string('cOrigem', 4)->nullable();
            $table->integer('nCodTitRepet')->nullable();
            $table->string('cGrupo', 20)->nullable();
            $table->integer('nCodMovCC')->nullable();
            $table->decimal('nValorMovCC', 15, 2)->nullable();
            $table->integer('nCodMovCCRepet')->nullable();
            $table->decimal('nDesconto', 15, 2)->nullable();
            $table->decimal('nJuros', 15, 2)->nullable();
            $table->decimal('nMulta', 15, 2)->nullable();
            $table->integer('nCodBaixa')->nullable();
            $table->date('dDtCredito')->nullable();
            $table->date('dDtConcilia')->nullable();
            $table->string('cHrConcilia', 8)->nullable();
            $table->string('cUsConcilia', 10)->nullable();
            $table->date('dDtInc')->nullable();
            $table->string('cHrInc', 8)->nullable();
            $table->string('cUsInc', 10)->nullable();
            $table->date('dDtAlt')->nullable();
            $table->string('cHrAlt', 8)->nullable();
            $table->string('cUsAlt', 10)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('omie_mf');
    }
}
