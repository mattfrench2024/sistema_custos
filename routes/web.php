<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationInternalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\CostBaseController;
use App\Http\Controllers\FinancialDashboardController;
use App\Http\Controllers\CostEntryController;
use App\Http\Controllers\CostsDashboardController;
use App\Http\Controllers\FinanceiroNotaController;
use App\Http\Controllers\OmieClienteController;
use App\Http\Controllers\OmiePagarController;
use App\Http\Controllers\OmieReceberController;
use App\Http\Controllers\FinanceiroAnaliticoController;
use App\Http\Controllers\OmieCategoriaController;
use App\Http\Controllers\OmieContaCorrenteController;
use App\Http\Controllers\OmieContratoController;



/*
|--------------------------------------------------------------------------
| ÁREA PÚBLICA
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});



Route::get('/financeiro/recebimentos-sync', [\App\Http\Controllers\RecebimentosSyncController::class, 'index'])
    ->name('financeiro.recebimentos.sync');


/*
|--------------------------------------------------------------------------
| REDIRECIONAMENTO PÓS LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->get('/redirect-by-role', function () {

        $role = auth()->user()->role->nome ?? null;

        return match ($role) {
            'ti_admin'   => redirect()->route('dashboard.admin'),
            'financeiro' => redirect()->route('dashboard.financeiro'),
            'rh'         => redirect()->route('dashboard.rh'),
            'auditoria'  => redirect()->route('dashboard.auditoria'),
            default      => redirect()->route('dashboard'),
        };
    })
    ->name('redirect.by.role');



/*
|--------------------------------------------------------------------------
| ÁREA LOGADA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARDS
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'default'])
        ->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::get('/admin',      [DashboardController::class, 'admin'])->name('dashboard.admin');
        Route::get('/financeiro', [FinancialDashboardController::class, 'index'])->name('dashboard.financeiro');
        Route::get('/rh',         [DashboardController::class, 'rh'])->name('dashboard.rh');
        Route::get('/auditoria',  [DashboardController::class, 'auditoria'])->name('dashboard.auditoria');
    });



    /*
    |--------------------------------------------------------------------------
    | TI ADMIN (ADMINISTRAÇÃO DO SISTEMA)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:ti_admin')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('settings', SettingController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('product_prices', ProductPriceController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::resource('payrolls', PayrollController::class);
    });



    /*
    |--------------------------------------------------------------------------
    | CUSTOS / FINANCEIRO – ACESSO PARA TODOS OS USUÁRIOS LOGADOS
    |--------------------------------------------------------------------------
    */
    Route::resource('expenses', ExpenseController::class);

    Route::resource('invoices', InvoiceController::class)
        ->only(['index', 'show']);


    /*
    |--------------------------------------------------------------------------
    | NOTAS FISCAIS (NOVO SISTEMA)
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/notas/{id}/{month}', [FinanceiroNotaController::class, 'show'])
        ->name('financeiro.notas.show');

    Route::post('/financeiro/notas/{id}/{month}', [FinanceiroNotaController::class, 'store'])
        ->name('financeiro.notas.store');


    /*
    |--------------------------------------------------------------------------
    | CUSTOS BASE
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/custos', [CostBaseController::class, 'index'])
        ->name('financeiro.costs.index');

    Route::get('/financeiro/custos/{cost}/editar', [CostBaseController::class, 'edit'])
        ->name('financeiro.costs.edit');

    Route::post('/financeiro/custos/{cost}', [CostBaseController::class, 'update'])
        ->name('financeiro.costs.update');


        //area omie




    /*
    |--------------------------------------------------------------------------
    | LANÇAMENTOS DE CUSTOS
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/lancamentos/novo', [CostEntryController::class, 'create'])
        ->name('financeiro.cost_entries.create');

    Route::post('/financeiro/lancamentos', [CostEntryController::class, 'store'])
        ->name('costs.store');

        Route::get('/financeiro/receber', [\App\Http\Controllers\ReceberController::class, 'index'])
    ->name('financeiro.receber.index');
    /*
|--------------------------------------------------------------------------
| A PAGAR
|--------------------------------------------------------------------------
*/

Route::get('/financeiro/pagar', [FinancialDashboardController::class, 'index'])
    ->name('financeiro.pagar.index');
    /*
    |--------------------------------------------------------------------------
    | ROTAS PÚBLICAS DE CUSTOS PARA DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/costs/{id}', [FinancialDashboardController::class, 'show'])
        ->name('costs.show');

    Route::get('/costs/{id}/current-month', [FinancialDashboardController::class, 'showCurrentMonth'])
        ->name('costs.currentMonth');


// ROTAS OMIE
Route::prefix('omie/{empresa}')
    ->name('omie.')
    ->middleware(['auth', 'verified', 'omie.empresa'])
    ->group(function () {

        /**
         * ============================
         * CONTAS A PAGAR
         * ============================
         */
        Route::get('/pagar', [OmiePagarController::class, 'index'])
            ->name('pagar.index');

        Route::get('/pagar/{pagar:codigo_lancamento_omie}', [OmiePagarController::class, 'show'])
            ->name('pagar.show');

        /**
         * ============================
         * CONTAS A RECEBER
         * ============================
         */
        Route::get('/receber', [OmieReceberController::class, 'index'])
            ->name('receber.index');

        Route::get('/receber/{receber}', [OmieReceberController::class, 'show'])
            ->name('receber.show');

        /**
         * ============================
         * CONTAS CORRENTES
         * ============================
         */
        Route::get('/contas-correntes', [OmieContaCorrenteController::class, 'index'])
            ->name('contas-correntes.index');

        Route::get('/contas-correntes/{contaCorrente}', [OmieContaCorrenteController::class, 'show'])
            ->name('contas-correntes.show');

        /**
         * ============================
         * CONTRATOS
         * ============================
         */
        Route::get('/contratos', [OmieContratoController::class, 'index'])
            ->name('contratos.index');

        Route::get('/contratos/{contrato}', [OmieContratoController::class, 'show'])
            ->name('contratos.show');
            /**
         * ============================
         * CLIENTES E FORNECEDORES
         * ============================
         */
            Route::get('/clientes', [OmieClienteController::class, 'index'])
            ->name('clientes.index');

        Route::get('/clientes/{cliente}', [OmieClienteController::class, 'show'])
            ->name('clientes.show');
    });


Route::get(
    '/financeiro/analitico/empresa/{empresa}',
    [FinanceiroAnaliticoController::class, 'empresa']
)->name('financeiro.analitico.empresa');
Route::prefix('omie')->group(function () {
    Route::get('/categorias/{empresa}', [OmieCategoriaController::class, 'index'])
        ->name('omie.categorias.index');
          Route::get('/categorias/{empresa}/{codigo}', [OmieCategoriaController::class, 'show'])
        ->name('omie.categorias.show');
});



    //omie analítico
    Route::get('/financeiro/analitico', [FinanceiroAnaliticoController::class, 'dashboard'])
    ->name('financeiro.analitico.dashboard');

    /*
    |--------------------------------------------------------------------------
    | RH / DP
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:rh,ti_admin')->group(function () {

        Route::resource('payrolls', PayrollController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::resource('expenses', ExpenseController::class)
            ->only(['index', 'show']);

        Route::resource('products', ProductController::class)
            ->except(['destroy']);

        Route::resource('categories', CategoryController::class)
            ->except(['destroy']);

        Route::resource('product_prices', ProductPriceController::class)
            ->except(['destroy']);

        Route::get('/rh/area-custos', [CostEntryController::class, 'index'])
            ->name('rh.costs.index');
    });



    /*
    |--------------------------------------------------------------------------
    | COST ENTRIES (ACESSO GERAL)
    |--------------------------------------------------------------------------
    */
    Route::resource('cost_entries', CostEntryController::class)
        ->except(['show']);



    /*
    |--------------------------------------------------------------------------
    | AUDITORIA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:auditoria,ti_admin')->group(function () {

        Route::get('/auditoria', [CostsDashboardController::class, 'index'])
            ->name('dashboard.auditoria');

        Route::get('/logs', [AuditLogController::class, 'index'])
            ->name('logs.index');

        Route::get('/invoices/auditoria', [InvoiceController::class, 'index'])
    ->name('invoices.audit');


        Route::get('/expenses/auditoria', [ExpenseController::class, 'index'])
            ->name('expenses.audit');
    });



    /*
    |--------------------------------------------------------------------------
    | NOTIFICAÇÕES INTERNAS
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [NotificationInternalController::class, 'index'])
        ->name('notifications.index');



    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/',     [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/',   [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/',  [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});



/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
