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
use App\Http\Controllers\CostNoteController;


/*
|--------------------------------------------------------------------------
| ÁREA PÚBLICA
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('redirect.by.role')
        : view('welcome');
});

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
    | TI ADMIN
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
    | FINANCEIRO
    |--------------------------------------------------------------------------
    */
   /*
/*
|--------------------------------------------------------------------------
| CUSTOS – ACESSO PARA TODOS OS USUÁRIOS LOGADOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // despesas (financeiro edita)
    Route::resource('expenses', ExpenseController::class);

    // invoices (somente leitura)
    Route::resource('invoices', InvoiceController::class)
        ->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | CATEGORIAS DE CUSTOS BASE
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/custos', [CostBaseController::class, 'index'])
        ->name('financeiro.costs.index');

    Route::get('/financeiro/custos/{cost}/editar', [CostBaseController::class, 'edit'])
        ->name('financeiro.costs.edit');

    Route::post('/financeiro/custos/{cost}', [CostBaseController::class, 'update'])
        ->name('financeiro.costs.update');

    /*
    |--------------------------------------------------------------------------
    | LANÇAMENTOS DE CUSTOS
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/lancamentos/novo', [CostEntryController::class, 'create'])
        ->name('financeiro.cost_entries.create');

    Route::post('/financeiro/lancamentos', [CostEntryController::class, 'store'])
        ->name('costs.store');

    /*
    |--------------------------------------------------------------------------
    | NOTAS FISCAIS POR MÊS (GET/POST)
    |--------------------------------------------------------------------------
    | /financeiro/notas/{cost}/{mes}
    |--------------------------------------------------------------------------
    */
    Route::get('/financeiro/notas/{cost}/{mes}', [CostNoteController::class, 'show'])
        ->name('financeiro.notas.show');

    Route::post('/financeiro/notas/{cost}/{mes}', [CostNoteController::class, 'save'])
        ->name('financeiro.notas.save');

});


    /*
    |--------------------------------------------------------------------------
    | ROTAS PÚBLICAS DE CUSTOS PARA DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/costs/{id}', [FinancialDashboardController::class, 'show'])
        ->name('costs.show');

    Route::get('/costs/{id}/current-month', [FinancialDashboardController::class, 'showCurrentMonth'])
        ->name('costs.currentMonth');
 // Notas (anexos e valores por mês)
Route::get('/public/notas/{cost}/{mes}', [CostAttachmentController::class, 'show'])
        ->name('financeiro.notas.show');

    Route::post('/notas/{cost}/{mes}', [CostAttachmentController::class, 'store'])
        ->name('financeiro.notas.store');
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

        // área de custos RH
        Route::get('/rh/area-custos', [CostEntryController::class, 'index'])
            ->name('rh.costs.index');
    });

    /*
    |--------------------------------------------------------------------------
    | ROTAS DE COST_ENTRIES SEM MIDDLEWARE ESPECÍFICO
    |--------------------------------------------------------------------------
    |
    | ⚠️ Mantidas porque fazem parte do seu código original
    | e podem ser usadas por múltiplos papéis
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
        Route::get('/logs',     [AuditLogController::class, 'index'])->name('logs.index');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.audit');
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.audit');
    });

    /*
    |--------------------------------------------------------------------------
    | Página informativa do papel
    |--------------------------------------------------------------------------
    */
    Route::get('/role-info', function () {
        return view('dashboards.role-info');
    })->name('role.info');

    /*
    |--------------------------------------------------------------------------
    | Notificações internas
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [NotificationInternalController::class, 'index'])
        ->name('notifications.index');

    /*
    |--------------------------------------------------------------------------
    | Perfil
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
