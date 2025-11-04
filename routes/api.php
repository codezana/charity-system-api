<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    IndexController,
    ReportController,
    AuthController,
    AidController,
    DebtController,
    ExpenseController,
    PeopleController,
    CaseController,
    CategoryController,
    DonationController,
    ProjectController,
};

Route::middleware('json')->group(function () {

    // Auth
    Route::post('login', [AuthController::class, 'login']);


    Route::middleware('auth:api')->group(function () {

        // Auth
        Route::post('logout', [AuthController::class, 'logout']);

        // User
        Route::get('user', [AuthController::class, 'index']);
        Route::get('user/{id}', [AuthController::class, 'show']);
        Route::put('user/{id}', [AuthController::class, 'update']);
        Route::post('store', [AuthController::class, 'store']);
        Route::delete('user/{id}', [AuthController::class, 'destroy']);
        Route::post('reset', [AuthController::class, 'resetPassword']);

        // Project
        Route::apiResource('projects', ProjectController::class);

        // Debts
        Route::apiResource('debts', DebtController::class);

        //View debts
        Route::get('viewdebt', [DebtController::class, 'viewdebt']);

        // Expenses
        Route::apiResource('expenses', ExpenseController::class);

        // Aid
        Route::post('aid',[ AidController::class, 'update']);

        // People
        Route::apiResource('people', PeopleController::class);
        Route::post('people/{id}', [PeopleController::class, 'recived']);

        // Filter case
        Route::get('filtterCase', [AidController::class, 'filtterCase']);

        // Case
        Route::apiResource('case', CaseController::class);

        // Category
        Route::apiResource('category', CategoryController::class);

        // Donation
        Route::apiResource('donation', DonationController::class);

        // Index    
        Route::get('/', [IndexController::class, 'index']);
        Route::get('/home', [IndexController::class, 'index']);


        // Reports
        Route::get('/report/project-summary', [ReportController::class, 'projectSummary']);
        Route::get('/report/donation-report', [ReportController::class, 'donationReport']);
        Route::get('/report/expense-report', [ReportController::class, 'expenseReport']);
        Route::get('/report/aid-distribution-report', [ReportController::class, 'aidDistributionReport']);
    });
});
