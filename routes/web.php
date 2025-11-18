<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| AUTH (guest)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| LOGOUT (auth)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| HOME REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'homeRedirect'])->name('home.redirect');

/*
|--------------------------------------------------------------------------
| DASHBOARD & PROFILE (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA (auth + role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin_internal,admin_komersial']) // pakai | jika spatie
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| DOCUMENTS (auth + role user [+ admin?])
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('documents')->name('documents.')->group(function () {

    // aksi tambahan
    Route::post('bulk', [DocumentController::class, 'bulk'])->name('bulk');

    Route::get('{document}/print',        [DocumentController::class, 'print'])->name('print');
    Route::get('{document}/print-pdf',    [DocumentController::class, 'printPdf'])->name('print-pdf');
    Route::get('{document}/tanda-terima', [DocumentController::class, 'printTandaTerima'])->name('print-tandaterima');

    Route::post('{document}/reject',      [DocumentController::class, 'reject'])->name('reject');

    Route::get('{document}/photo',        [DocumentController::class, 'photo'])->name('photo');
    Route::post('{document}/photo',       [DocumentController::class, 'photoStore'])->name('photo.store');

    Route::get('{document}/sign',         [DocumentController::class, 'sign'])->name('sign');
    Route::post('{document}/sign',        [DocumentController::class, 'signStore'])->name('sign.store');

    // resource CRUD utama (pakai '' bukan '/')
    Route::resource('', DocumentController::class)
        ->parameters(['' => 'document'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);
});

// HAPUS route lama yang bentrok seperti prefix('serahterima')->resource('documents', ...)
