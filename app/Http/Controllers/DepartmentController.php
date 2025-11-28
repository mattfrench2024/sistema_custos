<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        return Department::create($data);
    }

    public function show(Department $department)
    {
        return $department;
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $department->update($data);

        return $department;
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return response()->json(['message' => 'Departamento removido']);
    }
}
