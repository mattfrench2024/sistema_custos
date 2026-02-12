<?php

namespace App\Http\Controllers;

use App\Models\OmieCliente;
use App\Models\OmieContaCorrente;
use App\Services\Omie\OmieClient;
use Illuminate\Http\Request;

class OmieClienteController extends Controller
{
    protected $empresas = [
    'sv' => '04',
    'vs' => '30',
    'gv' => '36',
    'cs' => '10',
];

protected $empresaNomes = [
    'sv' => 'Sociedade Advogados Verreschi',
    'vs' => 'Verreschi Soluções',
    'gv' => 'Grupo Verreschi',
    'cs' => 'Consultoria Soluções',
];

public function index(string $empresa, Request $request)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId = $this->empresas[$empresa];

    $clientes = OmieCliente::where('empresa', $empresaId)
        ->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('razao_social', 'like', "%{$request->search}%")
                    ->orWhere('cnpj_cpf', 'like', "%{$request->search}%");
            });
        })
        ->orderBy('razao_social')
        ->paginate(20)
        ->withQueryString();

    return view('omie.clientes.index', [
        'clientes' => $clientes,
        'empresa'  => $empresa,
        'empresaLabel' => $this->empresaNomes[$empresa],
    ]);
}


    public function show(string $empresa, OmieCliente $cliente)
{
    if (
        ! isset($this->empresas[$empresa]) ||
        $cliente->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    return view('omie.clientes.show', compact('cliente', 'empresa'));
}
public function create(string $empresa)
{
    if (!isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaLabel = $this->empresaNomes[$empresa];
    $empresaCodigo = $this->empresas[$empresa];

    // Tags únicas existentes
    $tagsUnicas = OmieCliente::whereNotNull('tags')
        ->get()
        ->pluck('tags')
        ->flatten(1)
        ->pluck('tag')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    // Contas correntes ativas da empresa
    $contasCorrentes = OmieContaCorrente::where('empresa_codigo', $empresaCodigo)
        ->where('inativo', 'N')
        ->orderBy('descricao')
        ->get();

    return view('omie.clientes.create', compact('empresa', 'empresaLabel', 'tagsUnicas', 'contasCorrentes'));
}
public function store(Request $request, string $empresa)
{
    if (!isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId = $this->empresas[$empresa];

    $request->validate([
        'payload.razao_social' => 'required|string|max:255',
        'payload.cnpj_cpf'     => 'required|string|max:30',
        'payload.pessoa_fisica' => 'required|in:S,N',
        'payload.email'        => 'nullable|email|max:255',
        'payload.telefone'     => 'nullable|string|max:50',
        // adicione mais validações conforme necessário
    ]);

    $payload = $request->input('payload', []);
    // Tags: usar selected_tags se vier do novo form
    if ($request->has('selected_tags')) {
        $tagsArray = json_decode($request->selected_tags, true);
        $payload['tags'] = array_map(fn($t) => ['tag' => $t], $tagsArray);
    } elseif ($request->filled('tags')) {
        $tags = array_filter(array_map(fn($t) => ['tag' => trim($t)], explode(',', $request->tags)));
        $payload['tags'] = $tags;
    }

    // Defaults para criação
    $payload['info'] = [
        'dInc'    => now()->format('d/m/Y'),
        'hInc'    => now()->format('H:i:s'),
        'uInc'    => auth()->user()->name ?? 'WEB',
        'dAlt'    => now()->format('d/m/Y'),
        'hAlt'    => now()->format('H:i:s'),
        'uAlt'    => auth()->user()->name ?? 'WEB',
        'cImpAPI' => 'N',
    ];
    $payload['inativo'] = 'N';

    // Tags (se vier do form)
    if ($request->filled('tags')) {
        $tags = array_filter(array_map(fn($t) => ['tag' => trim($t)], explode(',', $request->tags)));
        $payload['tags'] = $tags;
    }

    OmieCliente::create([
        'empresa'             => $empresaId,
        'razao_social'        => $payload['razao_social'] ?? null,
        'nome_fantasia'       => $payload['nome_fantasia'] ?? null,
        'cnpj_cpf'            => $payload['cnpj_cpf'] ?? null,
        'email'               => $payload['email'] ?? null,
        'telefone'            => $payload['telefone'] ?? null,
        'cidade'              => $payload['cidade'] ?? null,
        'estado'              => $payload['estado'] ?? null,
        'tags'                => $payload['tags'] ?? null,
        'payload'             => $payload,
    ]);

    return redirect()->route('omie.clientes.index', $empresa)
        ->with('success', 'Cliente criado com sucesso.');
}

public function edit(string $empresa, OmieCliente $cliente)
{
    if (!isset($this->empresas[$empresa]) || $cliente->empresa !== $this->empresas[$empresa]) {
        abort(404);
    }

    $empresaLabel = $this->empresaNomes[$empresa];

    return view('omie.clientes.edit', compact('empresa', 'cliente', 'empresaLabel'));
}

public function update(Request $request, string $empresa, OmieCliente $cliente)
{
    if (!isset($this->empresas[$empresa]) || $cliente->empresa !== $this->empresas[$empresa]) {
        abort(404);
    }

    $request->validate([
        'payload.razao_social' => 'required|string|max:255',
        'payload.cnpj_cpf'     => 'required|string|max:30',
        'payload.pessoa_fisica' => 'required|in:S,N',
        'payload.email'        => 'nullable|email|max:255',
        'payload.telefone'     => 'nullable|string|max:50',
    ]);

    $payload = array_merge($cliente->payload ?? [], $request->input('payload', []));

    // Atualiza auditoria
    $payload['info']['dAlt'] = now()->format('d/m/Y');
    $payload['info']['hAlt'] = now()->format('H:i:s');
    $payload['info']['uAlt'] = auth()->user()->name ?? 'WEB';

    // Tags
    if ($request->filled('tags')) {
        $tags = array_filter(array_map(fn($t) => ['tag' => trim($t)], explode(',', $request->tags)));
        $payload['tags'] = $tags;
    }

    $cliente->update([
        'razao_social'  => $payload['razao_social'] ?? null,
        'nome_fantasia' => $payload['nome_fantasia'] ?? null,
        'cnpj_cpf'      => $payload['cnpj_cpf'] ?? null,
        'email'         => $payload['email'] ?? null,
        'telefone'      => $payload['telefone'] ?? null,
        'cidade'        => $payload['cidade'] ?? null,
        'estado'        => $payload['estado'] ?? null,
        'tags'          => $payload['tags'] ?? $cliente->tags,
        'payload'       => $payload,
    ]);

    return redirect()->route('omie.clientes.show', ['empresa' => $empresa, 'cliente' => $cliente])
        ->with('success', 'Cliente atualizado com sucesso.');
}
}
