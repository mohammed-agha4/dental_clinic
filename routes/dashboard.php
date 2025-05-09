<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\PatientsController;
use App\Http\Controllers\Dashboard\ServicesController;
use App\Http\Controllers\Dashboard\UsersRolesController;
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




Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');


    Route::get('/patients/search', [PatientsController::class, 'search'])->name('patients.search');
    Route::get('/patients/trash', [PatientsController::class, 'trash'])->name('patients.trash');
    Route::put('/patients/{patient}/restore', [PatientsController::class, 'restore'])->name('patients.restore');
    Route::delete('/patients/{patient}/force-delete', [PatientsController::class, 'forceDelete'])->name('patients.force-delete');
    Route::resource('/patients', PatientsController::class);


    Route::get('/staff/trash', [StaffController::class, 'trash'])->name('staff.trash');
    Route::put('/staff/{staff}/restore', [StaffController::class, 'restore'])->name('staff.restore');
    Route::delete('/staff/{staff}/force-delete', [StaffController::class, 'forceDelete'])->name('staff.force-delete');
    Route::resource('/staff', StaffController::class);


    Route::resource('/services', ServicesController::class);


    Route::get('/appointments/get-available-slots', [AppointmentsController::class, 'getAvailableSlots'])
        ->name('appointments.getAvailableSlots');
    Route::get('/appointments/get-available-dentists', [AppointmentsController::class, 'getAvailableDentists'])
        ->name('appointments.getAvailableDentists');
    Route::get('/dashboard/appointments/get-available-staff', [AppointmentsController::class, 'getAvailableStaff']);
    Route::get('/appointments/trash', [AppointmentsController::class, 'trash'])->name('appointments.trash');
    Route::put('/appointments/{appointment}/restore', [AppointmentsController::class, 'restore'])->name('appointments.restore');
    Route::delete('/appointments/{appointment}/force-delete', [AppointmentsController::class, 'forceDelete'])->name('appointments.force-delete');
    Route::resource('/appointments', AppointmentsController::class);


    Route::resource('/service-staff', ServiceStaffController::class);


    Route::get('/visits/trash', [VisitsController::class, 'trash'])->name('visits.trash');
    Route::put('/visits/{visits}/restore', [VisitsController::class, 'restore'])->name('visits.restore');
    Route::delete('/visits/{visits}/force-delete', [VisitsController::class, 'forceDelete'])->name('visits.force-delete');
    Route::resource('/visits', VisitsController::class)->except(['create']);
    Route::get('visits/create/{appointment}', [VisitsController::class, 'create'])->name('visits.create');


    Route::resource('/payments', PaymentsController::class);
    Route::get('/payments/{payment}/receipt', [PaymentsController::class, 'receipt'])->name('payments.receipt');


    Route::get('/expenses/trash', [ExpensesController::class, 'trash'])->name('expenses.trash');
    Route::put('/expenses/{expenses}/restore', [ExpensesController::class, 'restore'])->name('expenses.restore');
    Route::delete('/expenses/{expenses}/force-delete', [ExpensesController::class, 'forceDelete'])->name('expenses.force-delete');
    Route::resource('/expenses', ExpensesController::class);
    Route::get('expense-report', [ExpensesController::class, 'report'])->name('expenses.report');


    Route::resource('/roles', RolesController::class);


    Route::resource('/user-roles', UsersRolesController::class);
    Route::get('/user-roles/{user_id}/{role_id}/edit', [UsersRolesController::class, 'editComposite'])
        ->name('user-roles.edit-composite');
    Route::delete('/user-roles/{user_id}/{role_id}', [UsersRolesController::class, 'destroyComposite'])
        ->name('user-roles.destroy-composite');
    Route::put('/user-roles/{user_id}/{role_id}', [UsersRolesController::class, 'updateComposite'])
        ->name('user-roles.update-composite');



});



Route::prefix('dashboard/inventory')->middleware('auth')->name('dashboard.inventory.')->group(function () {

    Route::resource('/suppliers', SuppliersController::class);
    Route::resource('/categories', CategoriesController::class);
    Route::get('/inventory/trash', [InventoryController::class, 'trash'])->name('inventory.trash');
    Route::put('/inventory/{inventory}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
    Route::delete('/inventory/{inventory}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.force-delete');
    Route::resource('/inventory', InventoryController::class);
    Route::resource('/inventory-transactions', InventoryTransactionsController::class);
});




Route::prefix('notifications')->middleware('auth')->group(function () {
    Route::get('/', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/mark-as-read/{id}', [NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/delete/{id}', [NotificationsController::class, 'deleteNotification'])->name('notifications.delete');
    Route::delete('/delete-all', [NotificationsController::class, 'deleteAllNotifications'])->name('notifications.deleteAll');
});











// Route::get('/dashboard/patients/search', [PatientsController::class, 'search'])->name('dashboard.patients.search')->middleware('auth');
