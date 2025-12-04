<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Atualização direta na tabela product_prices
        // Considerando que a planilha já foi processada e temos os dados em um array
        // Para um uso real, você poderia usar um seeder separado, mas para manter no padrão de migration:

        $data = [
            // Exemplo de como popular:
            // ['product_name' => 'HeadSet', 'month' => 8, 'year' => 2025, 'value' => 150.00],
            // ['product_name' => 'Leaf', 'month' => 8, 'year' => 2025, 'value' => 300.00],
        ];

        foreach ($data as $item) {
            // Buscar o id do produto
            $product = DB::table('products')->where('nome', $item['product_name'])->first();
            if ($product) {
                DB::table('product_prices')->insert([
                    'product_id' => $product->id,
                    'month' => $item['month'],
                    'year' => $item['year'],
                    'value' => $item['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Caso precise desfazer
        DB::table('product_prices')->truncate();
    }
};
