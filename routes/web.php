<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;

/*
|--------------------------------------------------------------------------
| Web Routes - Restaurant Management Software
|--------------------------------------------------------------------------
*/

// Root Landing
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('restaurant.dashboard') 
        : redirect()->route('login');
});

// Logout Route -> Clears Session & Redirects to Login screen
Route::match(['get', 'post'], '/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// =========================================================================
// 1. PUBLIC ROUTES (Accessible to customers without login)
// =========================================================================

// Customer-Facing Review Submission (Guests scanning table QR code)
Route::get('/review', [RestaurantController::class, 'showReviewForm'])->name('restaurant.review');
Route::post('/review/store', [RestaurantController::class, 'storeReview'])->name('restaurant.review.store');

// =========================================================================
// 2. PROTECTED MANAGEMENT ROUTES (Requires Staff / Admin Login)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    
    // Main Restaurant Dashboard
    Route::get('/dashboard', [RestaurantController::class, 'dashboard'])->name('restaurant.dashboard');
    Route::get('/dash', function() {
        return redirect()->route('restaurant.dashboard');
    })->name('dashboard');

    // 1. Orders Module
    Route::get('/orders', [RestaurantController::class, 'orders'])->name('restaurant.orders');
    Route::get('/orders/new', [RestaurantController::class, 'newOrder'])->name('restaurant.orders.new');
    Route::post('/orders/store', [RestaurantController::class, 'storeOrder'])->name('restaurant.orders.store');
    Route::post('/orders/{id}/status', [RestaurantController::class, 'updateOrderStatus'])->name('restaurant.orders.status');
    Route::post('/orders/{id}/pay', [RestaurantController::class, 'settleOrderPayment'])->name('restaurant.orders.pay');

    // 2. Food / Menu Module
    Route::get('/menu', [RestaurantController::class, 'menu'])->name('restaurant.menu');
    Route::post('/menu/store', [RestaurantController::class, 'storeDish'])->name('restaurant.menu.store');
    Route::post('/menu/{id}/update', [RestaurantController::class, 'updateDish'])->name('restaurant.menu.update');
    Route::post('/menu/{id}/delete', [RestaurantController::class, 'deleteDish'])->name('restaurant.menu.delete');
    Route::post('/menu/{id}/toggle-status', [RestaurantController::class, 'toggleDishStatus'])->name('restaurant.menu.toggle');

    // 3. Category Module
    Route::get('/categories', [RestaurantController::class, 'categories'])->name('restaurant.categories');
    Route::post('/categories/store', [RestaurantController::class, 'storeCategory'])->name('restaurant.categories.store');
    Route::post('/categories/{id}/update', [RestaurantController::class, 'updateCategory'])->name('restaurant.categories.update');
    Route::post('/categories/{id}/delete', [RestaurantController::class, 'deleteCategory'])->name('restaurant.categories.delete');

    // 4. Customer Satisfaction Admin (CSAT & Review QR Generator)
    Route::get('/customer-satisfaction', [RestaurantController::class, 'customerSatisfaction'])->name('restaurant.customer-satisfaction');
    Route::get('/customer-satisfaction/table-tent', [RestaurantController::class, 'tableTentTemplate'])->name('restaurant.customer-satisfaction.table-tent');
    Route::get('/customer-satisfaction/qr-download', [RestaurantController::class, 'downloadQrCode'])->name('restaurant.customer-satisfaction.qr-download');
    Route::post('/customer-satisfaction/review/{id}/delete', [RestaurantController::class, 'deleteReview'])->name('restaurant.customer-satisfaction.review.delete');

    // 5. Billing POS (Hidden for now; redirects to /orders)
    Route::redirect('/billing', '/orders')->name('restaurant.billing');
    Route::redirect('/payment', '/orders');

    // 6. Basic Reports
    Route::get('/reports', [RestaurantController::class, 'reports'])->name('restaurant.reports');

    // 7. Settings & Printing
    Route::get('/settings', [RestaurantController::class, 'settings'])->name('restaurant.settings');
    Route::post('/settings/update', [RestaurantController::class, 'updateSettings'])->name('restaurant.settings.update');
    Route::post('/settings/test-print', [RestaurantController::class, 'testDirectPrint'])->name('restaurant.settings.test-print');

    // 8. Direct Thermal Print API
    Route::post('/orders/{id}/direct-print', [RestaurantController::class, 'directPrintOrder'])->name('restaurant.orders.direct-print');
});

// Fallback Route
Route::fallback(function () {
    return auth()->check() 
        ? redirect()->route('restaurant.dashboard') 
        : redirect()->route('login');
});
