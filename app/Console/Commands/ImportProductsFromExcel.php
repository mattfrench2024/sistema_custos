<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\Product;

class ImportProductsFromExcel extends Command
{
    protected $signature = 'import:products 
                            {path : Caminho do arquivo Excel (ex: "C:\... \Custos Utilities.xlsx" ou storage/app/arquivo.xlsx)}
                            {--sheet=0 : Índice ou nome da folha (padrão 0)} 
                            {--has-header=1 : 1 se a planilha tem cabeçalho na primeira linha, 0 caso contrário}';

    protected $description = 'Importa produtos a partir de uma planilha Excel conforme regras definidas.';

    public function handle()
    {
        $path = $this->argument('path');
        $sheet = $this->option('sheet');
        $hasHeader = (bool) $this->option('has-header');

        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado em: {$path}");
            return 1;
        }

        $this->info("Lendo arquivo: {$path} (folha: {$sheet})");

        // Lê a planilha inteira como array
        try {
            $sheets = Excel::toArray(null, $path);
        } catch (\Throwable $e) {
            $this->error("Erro ao ler Excel: " . $e->getMessage());
            return 1;
        }

        // Seleciona a folha pedida (por índice ou nome)
        if (is_numeric($sheet)) {
            $index = (int)$sheet;
            if (!isset($sheets[$index])) {
                $this->error("Folha índice {$index} não encontrada.");
                return 1;
            }
            $rows = $sheets[$index];
        } else {
            // buscar por nome (array associativo possível dependendo do driver)
            $found = null;
            foreach ($sheets as $k => $v) {
                // tentar comparar se a folha tem nome — nota: toArray normalmente retorna array indexado
            }
            $this->error("Folha por nome não suportada por esta leitura; informe o índice da folha.");
            return 1;
        }

        $this->info("Total de linhas na folha: " . count($rows));

        // detectar colunas: assumimos
        // Coluna 0 = Categoria
        // Coluna 1 = AJUSTES (nome do item)
        // Colunas 2..N = valores mensais

        $startRow = 0;
        if ($hasHeader) {
            $startRow = 1;
        }

        $hasCodigoColumn = Schema::hasColumn('products', 'codigo');

        DB::beginTransaction();
        try {
            $inserted = 0;
            foreach (array_slice($rows, $startRow) as $i => $row) {
                // segurança: garantir que existam pelo menos 2 colunas
                if (!isset($row[0]) || !isset($row[1])) {
                    $this->warn("Linha " . ($i + $startRow + 1) . " pulada (coluna Categoria ou AJUSTES ausente).");
                    continue;
                }

                $categoriaNome = trim((string)$row[0]);
                $produtoNome = trim((string)$row[1]);

                if ($categoriaNome === '' || $produtoNome === '') {
                    $this->warn("Linha " . ($i + $startRow + 1) . " pulada (categoria ou nome vazio).");
                    continue;
                }

                // Criar categoria se não existir
                $category = Category::firstOrCreate(
                    ['nome' => $categoriaNome],
                    ['descricao' => null]
                );

                // Pegar valores mensais (a partir da coluna 2)
                $months = array_slice($row, 2);
                $numericValues = [];
                foreach ($months as $v) {
                    if (is_null($v) || (is_string($v) && trim($v) === '')) continue;
                    // Normaliza vírgula decimal para ponto e tenta float
                    if (is_string($v)) {
                        $try = str_replace('.', '', $v); // remove separador de milhares se houver
                        $try = str_replace(',', '.', $try);
                        if (is_numeric($try)) {
                            $numericValues[] = (float)$try;
                            continue;
                        }
                    }
                    if (is_numeric($v)) {
                        $numericValues[] = (float)$v;
                    }
                }

                $valor = null;
                if (count($numericValues) > 0) {
                    $valor = max($numericValues);
                } else {
                    $valor = 0.00; // ou null, a seu critério; DB descreve NOT NULL — usar 0.00 por padrão
                }

                // descricao: podemos salvar json dos meses ou null
                $descricao = null;
                // recomenda-se salvar JSON para rastreabilidade:
                $descricao = json_encode(array_map(function($v){ return $v; }, $months));

                // Criar produto (sem codigo inicial se for necessário atualizar depois)
                $productData = [
                    'nome' => $produtoNome,
                    'categoria_id' => $category->id,
                    'valor' => number_format((float)$valor, 2, '.', ''),
                    'descricao' => $descricao,
                ];

                // Criar produto
                $product = Product::create($productData);

                // Gerar codigo se a coluna existir: slug + ID incremental
                if ($hasCodigoColumn) {
                    $slug = Str::slug($produtoNome);
                    $codigo = "{$slug}-{$product->id}";
                    // garantir unicidade (em caso de colisão improvável)
                    $exists = Product::where('codigo', $codigo)->exists();
                    if ($exists) {
                        $codigo = "{$slug}-{$product->id}-" . Str::random(4);
                    }
                    $product->codigo = $codigo;
                    $product->save();
                }

                $inserted++;
                if ($inserted % 50 === 0) {
                    $this->info("Inseridos: {$inserted}...");
                }
            }

            DB::commit();
            $this->info("Importação finalizada. Total inseridos: {$inserted}");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Erro durante importação: " . $e->getMessage());
            return 1;
        }
    }
}
