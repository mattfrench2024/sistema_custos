<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        return Payroll::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'funcionario' => 'required|string|max:255',
            'salario' => 'required|numeric',
            'data_pagamento' => 'required|date'
        ]);

        return Payroll::create($data);
    }

    public function show(Payroll $payroll)
    {
        return $payroll;
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'funcionario' => 'required|string|max:255',
            'salario' => 'required|numeric',
            'data_pagamento' => 'required|date'
        ]);

        $payroll->update($data);

        return $payroll;
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return response()->json(['message' => 'Folha removida']);
    }
}
