<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\PatientsController;
use App\Http\Controllers\Dashboard\ServicesController;
use App\Http\Controllers\Dashboard\AppointmentsController;
use App\Http\Controllers\Dashboard\ServiceStaffController;
use App\Http\Controllers\Dashboard\StaffServiceController;
use App\Http\Controllers\Dashboard\NotificationsController;
use App\Http\Controllers\Dashboard\Visits\VisitsController;
use App\Http\Controllers\Dashboard\Inventory\ToolsController;
use App\Http\Controllers\Dashboard\Payment\ExpensesController;
use App\Http\Controllers\Dashboard\Payment\PaymentsController;
use App\Http\Controllers\Dashboard\Inventory\InventoryController;
use App\Http\Controllers\Dashboard\Inventory\SuppliersController;
use App\Http\Controllers\Dashboard\Inventory\CategoriesController;
use App\Http\Controllers\Dashboard\Inventory\InventoryTransactionsController;



Route::prefix('dashboard')->middleware('auth')->name('dashboard')->group(function() {

    Route::get('/', [DashboardController::class, 'index']);

});



Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function() {

    Route::resource('/patients', PatientsController::class);
    Route::resource('/staff', StaffController::class);
    Route::resource('/services', ServicesController::class);

    Route::get('/appointments/get-available-slots', [AppointmentsController::class, 'getAvailableSlots'])
    ->name('appointments.getAvailableSlots');

    Route::get('/appointments/get-available-dentists', [AppointmentsController::class, 'getAvailableDentists'])
    ->name('appointments.getAvailableDentists');
    Route::get('/dashboard/appointments/get-available-staff', [AppointmentsController::class, 'getAvailableStaff']);
    Route::resource('/appointments', AppointmentsController::class);

    Route::resource('/service-staff', ServiceStaffController::class);
});






Route::prefix('dashboard/inventory')->middleware('auth')->name('dashboard.inventory.')->group(function() {

    Route::resource('/suppliers', SuppliersController::class);
    Route::resource('/categories', CategoriesController::class);
    Route::resource('/inventory', InventoryController::class);
    Route::resource('/inventory-transactions', InventoryTransactionsController::class);

});


// Route::prefix('dashboard/visits/')->middleware('auth')->name('dashboard.visits.')->group(function () {
//     Route::get('/check-in', [PatientVisitController::class, 'showCheckInForm'])->name('check-in');
//     Route::post('/check-in', [PatientVisitController::class, 'checkIn'])->name('process-check-in');
//     Route::post('/emergency', [PatientVisitController::class, 'emergencyWalkIn'])->name('emergency');
// });

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function() {

    Route::resource('/visits', VisitsController::class)->except(['create']);
    Route::get('visits/create/{appointment}', [VisitsController::class, 'create'])->name('visits.create');


});

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function() {
    Route::resource('/payments', PaymentsController::class);
    Route::resource('/expenses', ExpensesController::class);
    Route::get('expense-report', [ExpensesController::class, 'report'])->name('expenses.report');
});



Route::prefix('notifications')->middleware('auth')->group(function() {
    Route::get('/', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/mark-as-read/{id}', [NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
});
