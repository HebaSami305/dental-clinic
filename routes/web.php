<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ToothRecordController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;




/*Route::get('/', function () {
    return view('welcome');
});*/

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::resource('patients', PatientController::class);

Route::resource('appointments', AppointmentController::class)->except('show');

Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
    ->name('appointments.status');

Route::put('patients/{patient}/teeth/{tooth}', [ToothRecordController::class, 'update'])
    ->whereNumber('tooth')
    ->name('patients.teeth.update');

Route::resource('patients.visits', VisitController::class)
    ->only(['create', 'store'])
    ->shallow();

Route::resource('visits', VisitController::class)
    ->only(['edit', 'update', 'destroy']);

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::post('visits/{visit}/invoice', [InvoiceController::class, 'store'])->name('visits.invoice.store');
Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::patch('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    Route::resource('users', UserController::class)->except('show');
});

require __DIR__.'/auth.php';
