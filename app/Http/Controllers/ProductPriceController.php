<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPrice;
use App\Models\Product;

class ProductPriceController extends Controller
{
    public function index()
    {
        $prices = ProductPrice::with('product')->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        return view('product_prices.index', compact('prices'));
    }

    public function create()
    {
        $products = Product::all();
        return view('product_prices.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'month'      => 'required|integer|between:1,12',
            'year'       => 'required|integer',
            'value'      => 'required|numeric',
        ]);

        ProductPrice::create($request->all());

        return redirect()->route('product_prices.index')->with('success', 'Preço criado com sucesso!');
    }

    public function edit(ProductPrice $productPrice)
    {
        $products = Product::all();
        return view('product_prices.edit', compact('productPrice', 'products'));
    }

    public function update(Request $request, ProductPrice $productPrice)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'month'      => 'required|integer|between:1,12',
            'year'       => 'required|integer',
            'value'      => 'required|numeric',
        ]);

        $productPrice->update($request->all());

        return redirect()->route('product_prices.index')->with('success', 'Preço atualizado com sucesso!');
    }

    public function destroy(ProductPrice $productPrice)
    {
        $productPrice->delete();
        return redirect()->route('product_prices.index')->with('success', 'Preço deletado com sucesso!');
    }
    public function report()
{
    $report = ProductPrice::with('product')
        ->selectRaw('product_id, month, year, SUM(value) as total')
        ->groupBy('product_id','month','year')
        ->orderBy('year','desc')
        ->orderBy('month','desc')
        ->get();

    return view('product_prices.report', compact('report'));
}
}
