<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class RoleController extends Controller
{
    /**
     * Lista todas as roles
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('nome')->get();

        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }

    /**
     * Cria uma nova role
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|unique:roles,nome',
            'descricao' => 'nullable|string'
        ]);

        try {
            $role = Role::create($request->only(['nome', 'descricao']));

            // Log automático
            AuditLog::create([
                'user_id' => auth()->id(),
                'acao' => 'Criou Role',
                'descricao' => "Criou a role {$role->nome}",
                'ip' => request()->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role criada com sucesso!',
                'data' => $role
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza uma role existente
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'nome' => "required|string|unique:roles,nome,{$id}",
            'descricao' => 'nullable|string'
        ]);

        try {
            $role->update($request->only(['nome','descricao']));

            AuditLog::create([
                'user_id' => auth()->id(),
                'acao' => 'Atualizou Role',
                'descricao' => "Atualizou a role {$role->nome} (ID {$role->id})",
                'ip' => request()->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role atualizada com sucesso!',
                'data' => $role
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui uma role
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        try {
            $role->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'acao' => 'Excluiu Role',
                'descricao' => "Excluiu a role {$role->nome} (ID {$role->id})",
                'ip' => request()->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao excluir role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
