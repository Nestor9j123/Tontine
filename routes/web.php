<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TontineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\AgentReceiptController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Route de debug pour diagnostiquer l'erreur 500
Route::get('/debug', function () {
    try {
        $dbConnection = DB::connection()->getPdo();
        $dbStatus = 'Connected';
    } catch (\Exception $e) {
        $dbStatus = 'Failed: ' . $e->getMessage();
    }
    
    // Vérifier les utilisateurs en base
    try {
        $users = DB::table('users')->select('id', 'name', 'email', 'created_at')->get();
        $usersInfo = $users->toArray();
    } catch (\Exception $e) {
        $usersInfo = 'Error: ' . $e->getMessage();
    }
    
    return response()->json([
        'status' => 'Laravel OK',
        'database' => $dbStatus,
        'users_in_db' => $usersInfo,
        'env' => app()->environment(),
        'debug' => config('app.debug'),
        'url' => config('app.url'),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'storage_writable' => is_writable(storage_path()),
        'logs_path' => storage_path('logs'),
        'cache_path' => storage_path('framework/cache'),
    ]);
});



// Changement de langue
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Routes protégées par authentification
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Products
    // Tout le monde peut voir la liste et les détails
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Secretary peut créer
    Route::middleware('role:secretary|super_admin')->group(function () {
        Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
    });
    
    // Super Admin uniquement pour edit/delete
    Route::middleware('role:super_admin')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::resource('users', \App\Http\Controllers\UserController::class);
        
        // Paramètres système (Super Admin uniquement)
        Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('system-settings.index');
        Route::match(['post', 'put'], '/system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
        Route::post('/system-settings/apply-preset', [SystemSettingsController::class, 'applyPreset'])->name('system-settings.apply-preset');
        Route::post('/system-settings/reset-theme', [SystemSettingsController::class, 'resetTheme'])->name('system-settings.reset-theme');
        
        // Administration avancée (Super Admin uniquement)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/deleted-clients', [AdminController::class, 'deletedClients'])->name('deleted-clients');
            Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
            
            // Gestion des suppressions
            Route::get('/clients/{client}/preview-deletion', [AdminController::class, 'previewClientDeletion'])->name('clients.preview-deletion');
            Route::delete('/clients/{client}/force-delete', [AdminController::class, 'forceDeleteClient'])->name('clients.force-delete');
            Route::post('/clients/{clientId}/restore', [AdminController::class, 'restoreClient'])->name('clients.restore');
            
            // Nettoyage des données
            Route::post('/clean-orphaned-data', [AdminController::class, 'cleanOrphanedData'])->name('clean-orphaned-data');
        });
    });
    
    // Clients
    Route::resource('clients', ClientController::class);
    
    // Tontines
    Route::resource('tontines', TontineController::class);
    Route::post('tontines/{tontine}/validate', [TontineController::class, 'validateTontine'])
        ->name('tontines.validate')
        ->middleware('role:secretary|super_admin');
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/validate', [PaymentController::class, 'validatePayment'])
        ->name('payments.validate')
        ->middleware('role:secretary|super_admin');
    
    // Routes 2FA
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/', [TwoFactorController::class, 'index'])->name('index');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
        Route::post('/regenerate-backup-codes', [TwoFactorController::class, 'regenerateBackupCodes'])->name('regenerate-backup-codes');
        Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::get('/challenge', [TwoFactorController::class, 'challenge'])->name('challenge');
        Route::post('/challenge', [TwoFactorController::class, 'processChallenge'])->name('challenge.process');
    });

    // Rapports Avancés
    Route::get('/reports/advanced', [ReportsController::class, 'index'])->name('reports.advanced');
    Route::post('/reports/advanced/data', [ReportsController::class, 'getData'])->name('reports.advanced.data');
    Route::get('/reports/advanced/export/{type}', [ReportsController::class, 'export'])->name('reports.advanced.export');
    
    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    // API pour charger les tontines d'un client (utilisée par le formulaire de paiement)
    Route::get('api/clients/{client}/tontines', function ($clientId) {
        try {
            $tontines = \App\Models\Tontine::with(['product', 'client'])
                ->where('client_id', $clientId)
                ->where('status', 'active')
                ->get()
                ->map(function ($tontine) {
                    return [
                        'id' => $tontine->id,
                        'product_name' => $tontine->product->name,
                        'total_amount' => $tontine->total_amount,
                        'paid_amount' => $tontine->paid_amount,
                        'remaining_amount' => $tontine->remaining_amount,
                        'progress_percentage' => $tontine->progress_percentage,
                        'status' => $tontine->status,
                    ];
                });
            
            return response()->json($tontines);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des tontines'], 500);
        }
    })->middleware('auth');

    // Route de test pour l'API des tontines
    Route::get('test/client/{client}/tontines', function ($clientId) {
        $tontines = \App\Models\Tontine::with(['product', 'client'])
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->get();
        
        return response()->json([
            'client_id' => $clientId,
            'tontines_count' => $tontines->count(),
            'tontines' => $tontines->map(function ($tontine) {
                return [
                    'id' => $tontine->id,
                    'product_name' => $tontine->product->name,
                    'total_amount' => $tontine->total_amount,
                    'paid_amount' => $tontine->paid_amount,
                    'remaining_amount' => $tontine->remaining_amount,
                    'progress_percentage' => $tontine->progress_percentage,
                    'status' => $tontine->status,
                ];
            })
        ]);
    })->middleware('auth');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])
        ->name('payments.reject')
        ->middleware('role:secretary|super_admin');
    
    // Agent Ranking (accessible à tous sauf agents)
    Route::get('agents/ranking', [\App\Http\Controllers\AgentRankingController::class, 'index'])
        ->name('agents.ranking')
        ->middleware('role:secretary|super_admin');
    
    // Réconciliation et détection de fraude
    Route::get('reconciliation', [\App\Http\Controllers\ReconciliationController::class, 'index'])
        ->name('reconciliation.index')
        ->middleware('role:secretary|super_admin');
    
    // Gestion de Stock (Super Admin et Secrétaire uniquement)
    Route::prefix('stock')->name('stock.')->middleware('role:secretary|super_admin')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/movements', [StockController::class, 'movements'])->name('movements');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/', [StockController::class, 'store'])->name('store');
    });
    
    // Livraison de tontine (décrémente le stock)
    Route::post('tontines/{tontine}/deliver', [StockController::class, 'deliverTontine'])
        ->name('tontines.deliver')
        ->middleware('role:secretary|super_admin|agent');
    
    // Carnets numériques et paiement de carnet physique
    Route::get('clients/{client}/notebook', [NotebookController::class, 'show'])->name('notebooks.show');
    Route::post('clients/{client}/notebook/pay', [NotebookController::class, 'payNotebook'])->name('notebooks.pay');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/clients', [ReportController::class, 'clients'])->name('clients');
        Route::get('/tontines', [ReportController::class, 'tontines'])->name('tontines');
        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('/agents', [ReportController::class, 'agents'])->name('agents');
        Route::get('/agents/{user}', [ReportController::class, 'agentDetails'])->name('agents.details');
        Route::get("/agents/{agent}/payments", [ReportController::class, "agentPayments"])->name("agents.payments");
        
        // Exports
        Route::get('/export/clients', [ReportController::class, 'exportClients'])->name('export.clients');
        Route::get('/export/tontines', [ReportController::class, 'exportTontines'])->name('export.tontines');
        Route::get('/export/payments', [ReportController::class, 'exportPayments'])->name('export.payments');
        Route::get('/export/pdf/payment/{payment}', [ReportController::class, 'exportPaymentPdf'])->name('export.payment.pdf');
    });

    // Routes pour la messagerie
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/conversations/{conversation}/read', [ChatController::class, 'updateReadStatus'])->name('read');
        Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('start');
    });

    // Routes pour les charges mensuelles
    Route::resource('expenses', MonthlyExpenseController::class);
    Route::get('expenses-report/monthly', [MonthlyExpenseController::class, 'monthlyReport'])->name('expenses.monthly-report');

    // Routes pour les rapports mensuels
    Route::resource('monthly-reports', MonthlyReportController::class)->except(['destroy']);
    
    // Route de suppression restreinte aux super admins uniquement
    Route::delete('monthly-reports/{monthlyReport}', [MonthlyReportController::class, 'destroy'])
        ->name('monthly-reports.destroy')
        ->middleware('role:super_admin');
    
    // Routes pour les notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('notifications/{notification}/delivered', [NotificationController::class, 'markAsDelivered'])->name('notifications.delivered');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('notifications/{uuid}/restore', [NotificationController::class, 'restore'])->name('notifications.restore')->middleware('role:super_admin|secretary');
    Route::delete('notifications/{uuid}/force-delete', [NotificationController::class, 'forceDelete'])->name('notifications.force-delete')->middleware('role:super_admin');
    
    // API routes pour les notifications
    Route::get('api/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.count');
    Route::get('api/notifications/recent', [NotificationController::class, 'getRecent'])->name('api.notifications.recent');
    
    // API route pour vérifier l'existence des rapports mensuels
    Route::get('api/monthly-reports/check', [MonthlyReportController::class, 'checkExists'])->name('api.monthly-reports.check');
    
    // API routes pour le dashboard
    Route::get('api/dashboard/charts', [\App\Http\Controllers\Api\DashboardApiController::class, 'getChartsData'])->name('api.dashboard.charts');
    Route::post('api/dashboard/clear-cache', [\App\Http\Controllers\Api\DashboardApiController::class, 'clearCache'])->name('api.dashboard.clear-cache');
    Route::post('monthly-reports-generate', [MonthlyReportController::class, 'generate'])->name('monthly-reports.generate');
    Route::get('monthly-reports/{monthlyReport}/pdf', [MonthlyReportController::class, 'exportPdf'])->name('monthly-reports.pdf');

    // Route pour marquer une tontine comme livrée (décrément stock)
    Route::post('tontines/{tontine}/deliver', [TontineController::class, 'markAsDelivered'])
        ->name('tontines.deliver')
        ->middleware('role:secretary|super_admin|agent');

    // Routes pour les reçus d'agents
    Route::prefix('agent-receipts')->name('agent-receipts.')->group(function () {
        Route::get('/', [AgentReceiptController::class, 'index'])->name('index');
        Route::get('/agents/{agent}', [AgentReceiptController::class, 'show'])->name('show');
        Route::get('/agents/{agent}/pdf', [AgentReceiptController::class, 'downloadPdf'])->name('pdf');
        Route::post('/download-all', [AgentReceiptController::class, 'downloadAllReceipts'])->name('download-all');
        
        // Routes pour l'agent connecté
        Route::get('/my-receipt', [AgentReceiptController::class, 'myReceipt'])->name('my-receipt');
        Route::get('/my-receipt/pdf', [AgentReceiptController::class, 'downloadMyReceipt'])->name('my-pdf');
    });
});

require __DIR__.'/auth.php';
