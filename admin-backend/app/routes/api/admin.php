<?php

use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeTopCategoryController;
use App\Http\Controllers\LeadCreditPackageController;
use App\Http\Controllers\LeadCreditPurchaseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\Report\BusinessReportController;
use App\Http\Controllers\Report\LeadReportController;
use App\Http\Controllers\Report\ProductReportController;
use App\Http\Controllers\Report\UserReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::prefix('admin')->group(function () {

        // Login, logout, and profile routes with explicit names
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware(['auth:admin-api', 'scopes:admin']);
        Route::put('/update', [AuthController::class, 'update'])->name('admin.update')->middleware(['auth:admin-api', 'scopes:admin']);
        Route::post('/profile', [AuthController::class, 'profile'])->name('admin.profile')->middleware(['auth:admin-api', 'scopes:admin']);
        Route::post('/password/change', [AuthController::class, 'changePassword'])->name('admin.password.change')->middleware(['auth:admin-api', 'scopes:admin']);

        // Authenticated routes with admin middleware
        Route::middleware(['auth:admin-api', 'scopes:admin'])->group(function () {

            // Explicitly name dashboard routes
            Route::prefix('dashboard')->group(function () {
                Route::get('/summary', [DashboardController::class, 'dashboard_summary'])->name('admin.dashboard.summary');
                Route::get('/today/stats', [DashboardController::class, 'todayStats'])->name('admin.dashboard.today');
                Route::get('/weekly/graph', [DashboardController::class, 'weeklyGraph'])->name('admin.dashboard.weekly');
            });

            // Categories routes with explicit name
            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

            // Users
            Route::get('/users', [UserReportController::class, 'index'])
                ->name('admin.users.index');
            Route::put('/users/{id}/status', [UserReportController::class, 'updateStatus'])
                ->name('admin.users.update.status');

            // Businesses
            Route::get('/businesses', [BusinessReportController::class, 'index'])
                ->name('admin.businesses.index');
            Route::put('/businesses/{id}/status', [BusinessReportController::class, 'updateStatus'])
                ->name('admin.businesses.update.status');

            // Products
            Route::get('/products', [ProductReportController::class, 'index'])
                ->name('admin.products.index');
            Route::put('/products/{id}/status', [ProductReportController::class, 'updateStatus'])
                ->name('admin.products.update.status');

            // Leads
            Route::get('/leads', [LeadReportController::class, 'index'])
                ->name('admin.leads.index');
            Route::get('/lead/{id}/proposals', [LeadReportController::class, 'proposals'])
                ->name('admin.leads.proposals');

            // Lead credit purchase
            Route::get('lead/credit/purchases', [LeadCreditPurchaseController::class, 'index'])->name('admin.credit.purchases');
            Route::put('lead/credit/purchases/{id}/status', [LeadCreditPurchaseController::class, 'updateStatus'])->name('admin.credit.purchases.status');

            // user account verifications
            Route::get('account/verifications', [AccountVerificationController::class, 'index'])->name('admin.account.verifications');
            Route::put('account/verifications/{id}/status', [AccountVerificationController::class, 'updateStatus'])->name('admin.account.verifications.status');


            // API resources
            Route::apiResources([
                'product/units' => ProductUnitController::class,
                'business/types' => BusinessTypeController::class,
                'locations' => LocationController::class,
                'lead/credit/packages' => LeadCreditPackageController::class,
                'home/top/categories' => HomeTopCategoryController::class,
            ]);
        });
    });
});
