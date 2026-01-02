<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/* LOGIN */

Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});


/* LOGOUT */
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware(['web', 'auth']);

/* HOME REDIRECT */
Route::middleware(['web'])->group(function () {
    Route::get('/', [AuthController::class, 'homeRedirect'])->name('home.redirect');
});

/* DASHBOARD USER (khusus role:user) */
Route::middleware(['web', 'auth', 'single.session', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

/* PROFILE (boleh semua yang login: user & admin) */
Route::middleware(['web', 'auth', 'single.session'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/* ADMIN AREA (auth + single.session + role admin) */
Route::middleware(['web', 'auth', 'single.session', 'role:admin_internal,admin_komersial'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // === ROUTE BARU: hitung user online (buat AJAX di dashboard) ===
        Route::get('/online-users-count', [AdminDashboardController::class, 'onlineUsersCount'])
            ->name('online-users-count');

        Route::resource('users', UserController::class)->except(['show']);

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
    });

/* DOCUMENTS (auth + single.session + role:user) */
Route::middleware(['web', 'auth', 'single.session', 'role:user'])
    ->prefix('documents')
    ->name('documents.')
    ->group(function () {

        Route::post('bulk', [DocumentController::class, 'bulk'])->name('bulk');

        Route::get('{document}/print',        [DocumentController::class, 'print'])->name('print');
        Route::get('{document}/print-pdf',    [DocumentController::class, 'printPdf'])->name('print-pdf');
        Route::get('{document}/tanda-terima', [DocumentController::class, 'printTandaTerima'])->name('print-tandaterima');

        Route::post('{document}/reject',      [DocumentController::class, 'reject'])->name('reject');

        Route::get('{document}/photo',        [DocumentController::class, 'photo'])->name('photo');
        Route::post('{document}/photo',       [DocumentController::class, 'photoStore'])->name('photo.store');

        Route::get('{document}/sign',         [DocumentController::class, 'sign'])->name('sign');
        Route::post('{document}/sign',        [DocumentController::class, 'signStore'])->name('sign.store');

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

Route::get('/debug-write', function () {
    file_put_contents(storage_path('logs/debug_route.txt'), "ROUTE_HIT\n", FILE_APPEND);
    return 'ok';
});
