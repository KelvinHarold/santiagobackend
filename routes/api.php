<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RevenueShareController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\NotificationController;

// Auth (Public) — Rate limited to 10 attempts per minute to prevent brute force
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// All protected routes must be inside auth:sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile', [UserController::class, 'updateProfile']);

    // Notifications — any authenticated user can fetch/mark their own
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);

    // Revenue Shares — accessible to any authenticated user (used for dashboard)
    Route::get('/revenue-shares', [RevenueShareController::class, 'index']);
    Route::get('/office-total', [RevenueShareController::class, 'officeTotal']);

    // Daily Summaries for Dashboard
    Route::get('/daily-expenses', [DailyReportController::class, 'dailyExpenses']);
    Route::get('/office-daily', [DailyReportController::class, 'officeDaily']);

    // Report viewing and downloading
    Route::middleware('permission:ReportView')->group(function () {
        Route::apiResource('daily-reports', DailyReportController::class)->only(['index']);
        Route::get('/daily-reports/{id}/download', [DailyReportController::class, 'download']);
    });

    // Report uploading
    Route::middleware('permission:ReportUpload')->group(function () {
        Route::apiResource('daily-reports', DailyReportController::class)->only(['store']);
        Route::delete('/daily-reports/{id}', [DailyReportController::class, 'destroy']);
    });

    // User management — admin-only routes
    Route::middleware('permission:UserManagement')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // Roles management
    Route::middleware('permission:Roles Management')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'getPermissions']);
    });

    // Permissions management
    Route::middleware('permission:Permission Management')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
    });

    // Expenses management
    Route::middleware('permission:ExpensesManagement')->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
        Route::apiResource('expense-categories', ExpenseCategoryController::class);
    });
});

