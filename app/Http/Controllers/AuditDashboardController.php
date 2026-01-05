<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:auditoria,ti_admin');
    }

    public function index()
    {
        // Totais gerais
        $totalLogs = AuditLog::count();
        $totalUsuarios = AuditLog::distinct('user_id')->count('user_id');
        $totalAcoes = AuditLog::distinct('action')->count('action');

        // Logs por dia (últimos 30 dias)
        $logsPorDia = AuditLog::select(
                DB::raw('DATE(created_at) as dia'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        // Ações mais executadas
        $acoesMaisUsadas = AuditLog::select('action', DB::raw('COUNT(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Usuários que mais geram logs
        $usuariosAtivos = AuditLog::select('user_name', DB::raw('COUNT(*) as total'))
            ->groupBy('user_name')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Heatmap de acessos por hora
        $porHora = AuditLog::select(
                DB::raw('HOUR(created_at) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // Últimos logs
        $ultimosLogs = AuditLog::latest()->take(20)->get();

        return view('dashboards.auditoria', compact(
            'totalLogs',
            'totalUsuarios',
            'totalAcoes',
            'logsPorDia',
            'acoesMaisUsadas',
            'usuariosAtivos',
            'porHora',
            'ultimosLogs'
        ));
    }
}
