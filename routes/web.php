<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/documents/{document}/print', [DocumentController::class, 'print'])->name('documents.print');

// routes/web.php
Route::get('/documents/{document}/print-pdf', [DocumentController::class, 'printPdf'])
    ->name('documents.print-pdf');

Route::get('/documents/{document}/tanda-terima', [DocumentController::class, 'printTandaTerima'])
    ->name('documents.print-tandaterima');

// routes/web.php
Route::get('/documents/{document}/photo', [DocumentController::class, 'photo'])->name('documents.photo');
Route::post('/documents/{document}/photo', [DocumentController::class, 'photoStore'])->name('documents.photo.store');


Route::get('/', fn() => redirect()->route('dashboard'));
// routes/web.php
Route::post('/documents/bulk', [DocumentController::class, 'bulk'])->name('documents.bulk');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/sign', [DocumentController::class, 'sign'])->name('documents.sign');


Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::get('/create', [DocumentController::class, 'create'])->name('create');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    Route::get('/{document}/sign', [DocumentController::class, 'sign'])->name('sign');
    Route::post('/{document}/sign', [DocumentController::class, 'signStore'])->name('sign.store'); // 🆕
});


Route::prefix('serahterima')->group(function () {
    Route::resource('documents', DocumentController::class);
});
