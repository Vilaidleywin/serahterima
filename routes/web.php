<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;

Route::get('/', fn()=>redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class,'index'])->name('index');
    Route::get('/create', [DocumentController::class,'create'])->name('create');
    Route::post('/', [DocumentController::class,'store'])->name('store');
    Route::get('/{document}/edit', [DocumentController::class,'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class,'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class,'destroy'])->name('destroy');
});


