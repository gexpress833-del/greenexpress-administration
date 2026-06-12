<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ExchangeRateController;
use App\Http\Controllers\Admin\MealController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionSuspensionController;
use App\Http\Controllers\Admin\SubscriptionTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Agent\PointsController;
use App\Http\Controllers\Agent\ReceiptController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRatePublicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/taux', [ExchangeRatePublicController::class, 'show'])->name('exchange-rate.show');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/history', [NotificationController::class, 'history'])->name('notifications.history');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('meals', MealController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'show', 'update']);
        Route::resource('subscription-types', SubscriptionTypeController::class)->except(['show']);
        Route::resource('deliveries', DeliveryController::class)->only(['index', 'create', 'store']);
        Route::resource('exchange-rates', ExchangeRateController::class)->only(['index', 'store']);
        Route::resource('commissions', CommissionController::class)->only(['index']);
        Route::resource('withdrawals', WithdrawalController::class)->only(['index', 'update']);
        Route::get('/suspensions', [SubscriptionSuspensionController::class, 'index'])->name('suspensions.index');
        Route::post('/suspensions/{suspension}/accept', [SubscriptionSuspensionController::class, 'accept'])->name('suspensions.accept');
        Route::post('/suspensions/{suspension}/reject', [SubscriptionSuspensionController::class, 'reject'])->name('suspensions.reject');
        Route::resource('complaints', ComplaintController::class)->only(['index', 'show', 'update']);
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
        // Reports
        Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'exportSales'])->name('reports.sales');
    });

    // Agent routes
    Route::middleware(['role:agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('orders', App\Http\Controllers\Agent\OrderController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('subscriptions', App\Http\Controllers\Agent\SubscriptionController::class)->only(['index', 'create', 'store']);
        Route::post('/subscriptions/{subscription}/generate-credentials', [App\Http\Controllers\Agent\SubscriptionController::class, 'generateCredentials'])->name('subscriptions.generate-credentials');
        Route::post('/subscriptions/{subscription}/update-client-info', [App\Http\Controllers\Agent\SubscriptionController::class, 'updateClientInfo'])->name('subscriptions.update-client-info');
        Route::get('/receipt/{order}', [ReceiptController::class, 'show'])->name('receipt.show');
        Route::get('/receipt/{order}/pdf', [ReceiptController::class, 'pdf'])->name('receipt.pdf');
        Route::get('/commissions', [App\Http\Controllers\Agent\CommissionController::class, 'index'])->name('commissions.index');
        Route::get('/points', [PointsController::class, 'index'])->name('points.index');
        Route::get('/withdrawals', [App\Http\Controllers\Agent\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [App\Http\Controllers\Agent\WithdrawalController::class, 'store'])->name('withdrawals.store');
    });

    // Livreur routes
    Route::middleware(['role:livreur'])->prefix('livreur')->name('livreur.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Livreur\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/deliveries', [App\Http\Controllers\Livreur\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [App\Http\Controllers\Livreur\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/assign', [App\Http\Controllers\Livreur\DeliveryController::class, 'assign'])->name('deliveries.assign');
        Route::get('/delivery-validate', [App\Http\Controllers\Livreur\DeliveryController::class, 'validateQrForm'])->name('deliveries.validate-qr-form');
        Route::post('/deliveries/{delivery}/validate-by-code', [App\Http\Controllers\Livreur\DeliveryController::class, 'validateByCode'])->name('deliveries.validate-by-code');
        Route::post('/deliveries/{delivery}/notify', [App\Http\Controllers\Livreur\DeliveryController::class, 'notifyClient'])->name('deliveries.notify');
    });

    // Cuisinier routes
    Route::middleware(['role:cuisinier'])->prefix('cuisinier')->name('cuisinier.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Cuisinier\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [App\Http\Controllers\Cuisinier\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Cuisinier\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Cuisinier\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/orders/{order}/print', [App\Http\Controllers\Cuisinier\OrderController::class, 'print'])->name('orders.print');
    });

    // Client routes
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/subscriptions', [App\Http\Controllers\Client\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}', [App\Http\Controllers\Client\SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/renew', [App\Http\Controllers\Client\SubscriptionController::class, 'renew'])->name('subscriptions.renew');
        Route::post('/subscriptions/{subscription}/suspend', [App\Http\Controllers\Client\SubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
        Route::post('/subscriptions/{subscription}/reactivate', [App\Http\Controllers\Client\SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
        Route::get('/orders', [App\Http\Controllers\Client\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Client\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('orders.review');
        Route::get('/deliveries', [App\Http\Controllers\Client\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [App\Http\Controllers\Client\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::get('/complaints', [App\Http\Controllers\Client\ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/orders/{order}/complaint', [App\Http\Controllers\Client\ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/orders/{order}/complaint', [App\Http\Controllers\Client\ComplaintController::class, 'store'])->name('complaints.store');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'store'])->name('password.change.store');
});

require __DIR__.'/auth.php';

// Public document/receipt verification page
Route::get('/verify', [DocumentVerificationController::class, 'show'])->name('verify.show');
