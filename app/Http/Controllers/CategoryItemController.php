<?php

namespace App\Http\Controllers;

use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryItemController extends Controller
{
    // Middleware para apenas financeiro acessar
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role_id !== 4) { // financeiro
                abort(403, 'Acesso negado.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $items = CategoryItem::orderBy('nome')->get();
        return view('category_items.index', compact('items'));
    }

    public function create()
    {
        // Carregar categorias direto da tabela costs_base
        $categorias = DB::table('costs_base')
            ->where('Categoria', '!=', 'Total Geral')
            ->orderBy('Categoria')
            ->pluck('Categoria');

        return view('category_items.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|unique:category_items,nome',
            'tipo' => 'nullable|string'
        ]);

        CategoryItem::create($request->all());

        return redirect()->route('category_items.index')
            ->with('success', 'Item criado com sucesso.');
    }

    public function edit(CategoryItem $category_item)
    {
        return view('category_items.edit', compact('category_item'));
    }

    public function update(Request $request, CategoryItem $category_item)
    {
        $request->validate([
            'nome' => 'required|unique:category_items,nome,' . $category_item->id,
            'tipo' => 'nullable|string'
        ]);

        $category_item->update($request->all());

        return redirect()->route('category_items.index')
            ->with('success', 'Item atualizado.');
    }

    public function destroy(CategoryItem $category_item)
    {
        $category_item->delete();
        return redirect()->route('category_items.index')
            ->with('success', 'Item removido.');
    }
}
