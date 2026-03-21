<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesTeamController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SalesTeam\RxController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [AuthController::class , 'login'])->name('login');
Route::post('/login', [AuthController::class , 'loginPost'])->name('login.submit');
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

    Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin-dashboard', [AdminController::class , 'dashboard'])->name('admin.dashboard');
            Route::get('/admin/dashboard-data', [AdminController::class , 'getDashboardData'])->name('admin.dashboard.data');
            Route::get('/admin/dashboard-export', [AdminController::class , 'exportDashboard'])->name('admin.dashboard.export');

            // Dynamic Data Fetching Routes
            Route::get('/get-zones', [SalesTeamController::class , 'getZones']);
            Route::get('/get-regions', [SalesTeamController::class , 'getRegions']);
            Route::get('/get-hqs', [SalesTeamController::class , 'getHqs']);
            Route::get('/get-designations', [SalesTeamController::class , 'getDesignations']);

            // Staff Management
            Route::get('/admin/sales-staff', [SalesTeamController::class , 'index'])->name('sales-staff.index');
            Route::get('/admin/create-sales-staff', [SalesTeamController::class , 'create'])->name('sales-staff.create');
            Route::post('/admin/store-sales-staff', [SalesTeamController::class , 'store'])->name('sales-staff.store');
            Route::get('/admin/sales-staff/{id}/edit', [SalesTeamController::class , 'edit'])->name('sales-staff.edit');
            Route::put('/admin/sales-staff/{id}', [SalesTeamController::class , 'update'])->name('sales-staff.update');
            Route::delete('/admin/sales-staff/{id}', [SalesTeamController::class , 'destroy'])->name('sales-staff.destroy');

            // Master Data Management
            Route::group(['prefix' => 'admin/master'], function () {
                    Route::get('/zones', [MasterDataController::class , 'zoneIndex'])->name('zones.index');
                    Route::post('/zones', [MasterDataController::class , 'zoneStore'])->name('zones.store');
                    Route::put('/zones/{id}', [MasterDataController::class , 'zoneUpdate'])->name('zones.update');
                    Route::delete('/zones/{id}', [MasterDataController::class , 'zoneDestroy'])->name('zones.destroy');

                    Route::get('/regions', [MasterDataController::class , 'regionIndex'])->name('regions.index');
                    Route::post('/regions', [MasterDataController::class , 'regionStore'])->name('regions.store');
                    Route::put('/regions/{id}', [MasterDataController::class , 'regionUpdate'])->name('regions.update');
                    Route::delete('/regions/{id}', [MasterDataController::class , 'regionDestroy'])->name('regions.destroy');

                    Route::get('/hqs', [MasterDataController::class , 'hqIndex'])->name('hqs.index');
                    Route::post('/hqs', [MasterDataController::class , 'hqStore'])->name('hqs.store');
                    Route::put('/hqs/{id}', [MasterDataController::class , 'hqUpdate'])->name('hqs.update');
                    Route::delete('/hqs/{id}', [MasterDataController::class , 'hqDestroy'])->name('hqs.destroy');

                    Route::get('/designations', [MasterDataController::class , 'designationIndex'])->name('designations.index');
                    Route::post('/designations', [MasterDataController::class , 'designationStore'])->name('designations.store');
                    Route::put('/designations/{id}', [MasterDataController::class , 'designationUpdate'])->name('designations.update');
                    Route::delete('/designations/{id}', [MasterDataController::class , 'designationDestroy'])->name('designations.destroy');
                }
                );
            }
            );

            Route::middleware(['role:sales_team'])->group(function () {
            Route::get('/rx-details', [RxController::class , 'index'])->name('rx.index');
            Route::get('/rx-details/export', [RxController::class , 'export'])->name('rx.export');
            Route::post('/rx-details', [RxController::class , 'store'])->name('rx.store');
            Route::put('/rx-details/{id}', [RxController::class , 'update'])->name('rx.update');
            Route::delete('/rx-details/{id}', [RxController::class , 'destroy'])->name('rx.destroy');
        }
        );
    });
