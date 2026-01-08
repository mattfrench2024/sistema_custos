<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieTipoDocumentoService;
use App\Models\OmieTipoDocumento;

class OmieImportTiposDocumento extends Command
{
    protected $signature = 'omie:import-tipos-documento {empresa : sv | vs | gv}';

    protected $description = 'Importa Tipos de Documento do Omie';

    public function handle(OmieTipoDocumentoService $service)
    {
        $empresaSlug = $this->argument('empresa');

        // Mapa slug -> código Omie (mesmo padrão do projeto)
        $map = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
        ];

        if (! isset($map[$empresaSlug])) {
            $this->error('Empresa inválida. Use: sv | vs | gv');
            return Command::FAILURE;
        }

        $empresaCodigo = $map[$empresaSlug];

        // 🔑 Credenciais vindas do config/omie.php
        $empresaCfg = config("omie.empresas.{$empresaCodigo}");

        if (! $empresaCfg || empty($empresaCfg['app_key'])) {
            $this->error("Configuração Omie não encontrada para a empresa {$empresaCodigo}");
            return Command::FAILURE;
        }

        $this->info(
            "📄 Importando Tipos de Documento — {$empresaCfg['label']} (empresa {$empresaCodigo})"
        );

        // 👉 chamada correta do service
        $tipos = $service->listar(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $importados = 0;

        foreach ($tipos as $tipo) {
            OmieTipoDocumento::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                ['descricao' => $tipo['descricao']]
            );

            $importados++;
        }

        $this->info("✅ {$importados} tipos de documento importados com sucesso.");

        return Command::SUCCESS;
    }
}
