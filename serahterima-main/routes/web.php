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


Route::get('/', fn()=>redirect()->route('dashboard'));
// routes/web.php
Route::post('/documents/bulk', [DocumentController::class, 'bulk'])->name('documents.bulk');
Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
Route::get('/documents/create', [DocumentController::class,'create'])->name('documents.create');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/sign', [DocumentController::class, 'sign'])->name('documents.sign');


Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class,'index'])->name('index');
    Route::get('/create', [DocumentController::class,'create'])->name('create');
    Route::post('/', [DocumentController::class,'store'])->name('store');
    Route::get('/{document}/edit', [DocumentController::class,'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class,'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class,'destroy'])->name('destroy');
});


