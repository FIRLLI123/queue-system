<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WebDashboardController;
use App\Http\Controllers\WebOrderController;
use App\Http\Controllers\WebOrderTypeController;
use App\Http\Controllers\WebUserController;
use App\Http\Controllers\WebChatController;
use App\Http\Controllers\WebReportController;
use App\Http\Controllers\WebTitipanOrderController;
use App\Http\Controllers\WebTitipanRequirementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// Authenticated Routes (CC + Admin)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [WebDashboardController::class, 'getDashboardData']);
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('password.update');
    
    // Get active order types for accept form
    Route::get('/order-types', [WebOrderTypeController::class, 'indexActive']);

    // Titipan requirements — read-only for everyone authenticated (needed for dropdown)
    Route::get('/titipan-requirements', [WebTitipanRequirementController::class, 'list'])->name('titipan-requirements.list');

    // Chat routes
    Route::post('/chats/send', [WebChatController::class, 'send']);
    Route::post('/chats/read', [WebChatController::class, 'markAsRead']);
});

// CC Only Actions
Route::middleware(['auth', 'role:CC'])->group(function () {
    Route::post('/orders/accept', [WebOrderController::class, 'accept']);
    Route::post('/orders/void', [WebOrderController::class, 'void']);
    Route::post('/orders/titipan/accept', [WebTitipanOrderController::class, 'accept']);
    Route::post('/queue/break', [WebDashboardController::class, 'startBreak']);
    Route::post('/queue/ready', [WebDashboardController::class, 'endBreak']);
});

// Admin Only Management Panel
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    // CC User Accounts CRUD
    Route::get('/admin/users', [WebUserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/data', [WebUserController::class, 'list']);
    Route::post('/admin/users', [WebUserController::class, 'store']);
    Route::get('/admin/users/{id}', [WebUserController::class, 'show']);
    Route::put('/admin/users/{id}', [WebUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [WebUserController::class, 'destroy']);
    Route::post('/admin/queue/reorder', [WebUserController::class, 'reorderQueue'])->name('admin.queue.reorder');

    // Order Types CRUD
    Route::get('/admin/order-types', [WebOrderTypeController::class, 'index'])->name('admin.order-types');
    Route::get('/admin/order-types/data', [WebOrderTypeController::class, 'list']);
    Route::post('/admin/order-types', [WebOrderTypeController::class, 'store']);
    Route::get('/admin/order-types/{id}', [WebOrderTypeController::class, 'show']);
    Route::put('/admin/order-types/{id}', [WebOrderTypeController::class, 'update']);
    Route::delete('/admin/order-types/{id}', [WebOrderTypeController::class, 'destroy']);

    // Titipan Orders CRUD — Admin only
    Route::get('/admin/titipan-orders', [WebTitipanOrderController::class, 'index'])->name('admin.titipan-orders');
    Route::get('/admin/titipan-orders/data', [WebTitipanOrderController::class, 'list']);
    Route::post('/admin/titipan-orders', [WebTitipanOrderController::class, 'store']);
    Route::get('/admin/titipan-orders/{id}', [WebTitipanOrderController::class, 'show']);
    Route::put('/admin/titipan-orders/{id}', [WebTitipanOrderController::class, 'update']);
    Route::delete('/admin/titipan-orders/{id}', [WebTitipanOrderController::class, 'destroy']);

    // Titipan Requirements CRUD — Admin only (manage the requirement options)
    Route::get('/admin/titipan-requirements', [WebTitipanRequirementController::class, 'index'])->name('admin.titipan-requirements');
    Route::get('/admin/titipan-requirements/data', [WebTitipanRequirementController::class, 'list']);
    Route::post('/admin/titipan-requirements', [WebTitipanRequirementController::class, 'store']);
    Route::get('/admin/titipan-requirements/{id}', [WebTitipanRequirementController::class, 'show']);
    Route::put('/admin/titipan-requirements/{id}', [WebTitipanRequirementController::class, 'update']);
    Route::delete('/admin/titipan-requirements/{id}', [WebTitipanRequirementController::class, 'destroy']);

    // Screen Monitoring
    Route::get('/admin/screen', [WebDashboardController::class, 'adminScreen'])->name('admin.screen');
    Route::get('/admin/screen/data', [WebDashboardController::class, 'getAdminScreenData']);

    // Report
    Route::get('/admin/report', [WebReportController::class, 'index'])->name('admin.report');
    Route::get('/admin/report/data', [WebReportController::class, 'getData']);
    Route::get('/admin/report/export', [WebReportController::class, 'export'])->name('admin.report.export');
});

